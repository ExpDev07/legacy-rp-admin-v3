<template>
    <div>

        <portal to="title">
            <div class="flex items-start space-x-10 mobile:flex-wrap">
                <h1 class="dark:text-white">
                    {{ character.name }} #{{ character.id }}
                </h1>
                <div class="flex items-center space-x-5 mobile:flex-wrap mobile:w-full mobile:!mr-0 mobile:!ml-0 mobile:space-x-0">
                    <badge class="bg-gray-100 border-gray-200 dark:bg-dark-secondary">
                        <span class="font-semibold">
                            {{ character.gender | formatGender(t) }}
                        </span>
                    </badge>
                    <badge class="bg-gray-100 border-gray-200 dark:bg-dark-secondary" v-html="local.birth">
                        {{ local.birth }}
                    </badge>
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="character.characterDeleted">
                        <span class="font-semibold">{{ t('players.edit.deleted') }}: {{ $moment(character.characterDeletionTimestamp).format('l') }}</span>
                    </badge>
                    <badge class="bg-gray-100 border-gray-200 dark:bg-dark-secondary relative">
                        <span v-html="local.cash" :title="local.cashTitle">{{ local.cash }}</span>

                        <button
                            class="block px-1 py-1 text-center text-black dark:text-white text-xs absolute top-0 right-0 bg-transparent rounded"
                            @click="isEditingBalance = true"
                            v-if="$page.auth.player.isSuperAdmin"
                        >
                            <i class="fas fa-edit"></i>
                        </button>
                    </badge>
                    <badge class="bg-gray-100 border-gray-200 dark:bg-dark-secondary" v-html="local.stocks">
                        {{ local.stocks }}
                    </badge>
                </div>
            </div>
        </portal>

        <portal to="actions">
            <div>
                <!-- Remove Tattoos -->
                <a href="#" class="px-5 py-2 font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3" @click="function(e) {e.preventDefault(); isTattooRemoval = true}">
                    <i class="fas fa-eraser"></i>
                    {{ t('players.characters.remove_tattoo') }}
                </a>
                <!-- Reset Spawn-point -->
                <a href="#" class="px-5 py-2 font-semibold text-white rounded bg-warning mr-3 dark:bg-dark-warning mobile:block mobile:w-full mobile:m-0 mobile:mb-3" @click="function(e) {e.preventDefault(); isResetSpawn = true}">
                    <i class="fas fa-heartbeat"></i>
                    {{ t('players.characters.reset_spawn') }}
                </a>
                <!-- Back -->
                <a class="px-5 py-2 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3" :href="'/players/' + player.steamIdentifier">
                    <i class="fas fa-backward"></i>
                    {{ t('global.back') }}
                </a>
            </div>
        </portal>

        <!-- Remove Tattoos -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isTattooRemoval">
            <div class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.sure_tattoos') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="zone">
                        {{ t('players.characters.tattoo_zone') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="zone">
                        <option value="all">{{ t('players.characters.zone.all') }}</option>
                        <option value="head" selected>{{ t('players.characters.zone.head') }}</option>
                        <option value="left_arm">{{ t('players.characters.zone.left_arm') }}</option>
                        <option value="right_arm">{{ t('players.characters.zone.right_arm') }}</option>
                        <option value="torso">{{ t('players.characters.zone.torso') }}</option>
                        <option value="left_leg">{{ t('players.characters.zone.left_leg') }}</option>
                        <option value="right_leg">{{ t('players.characters.zone.right_leg') }}</option>
                    </select>
                </div>
                <p v-html="t('players.characters.tattoo_no_undo')">
                    {{ t('players.characters.tattoo_no_undo') }}
                </p>
                <div class="flex justify-end mt-2">
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isTattooRemoval = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button" class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger" @click="removeTattoos">
                        {{ t('players.characters.tattoo_do') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Balance -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isEditingBalance">
            <div class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.sure_tattoos') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="cash">
                        {{ t('players.characters.edit_cash') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="cash" type="number" min="-1000000000" max="1000000000" v-model="balanceForm.cash" />
                </div>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="bank">
                        {{ t('players.characters.edit_bank') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="bank" type="number" min="-1000000000" max="1000000000" v-model="balanceForm.bank" />
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isEditingBalance = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button" class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger" @click="editBalance">
                        {{ t('players.characters.balance_do') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reset spawn -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isResetSpawn">
            <div class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.sure_spawn') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="spawn">
                        {{ t('players.characters.spawn_point') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="spawn">
                        <option v-for="coords in resetCoords" :key="coords" :value="coords">{{ t('players.characters.spawn.' + coords) }}</option>
                    </select>
                </div>
                <p v-html="t('players.characters.spawn_no_undo')">
                    {{ t('players.characters.spawn_no_undo') }}
                </p>
                <div class="flex justify-end mt-2">
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isResetSpawn = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button" class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger" @click="resetSpawn">
                        {{ t('players.characters.spawn_do') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Editing -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('global.info') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent="submit(false)">
                    <!-- Name -->
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="first_name">
                                {{ t('players.edit.prename') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="first_name" v-model="form.first_name">
                        </div>
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="last_name">
                                {{ t('players.edit.surname') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="last_name" v-model="form.last_name">
                        </div>
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2">
                                {{ t('players.edit.phone') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600 text-gray-400" :value="character.phoneNumber" disabled readonly />
                        </div>
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="backstory">
                            {{ t('players.edit.backstory') }}
                        </label>
                        <textarea class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="backstory" v-model="form.backstory"></textarea>
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="dob">
                            {{ t('players.edit.dob') }}
                        </label>
                        <input class="block w-56 px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="dob" v-model="form.date_of_birth">
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="gender">
                            {{ t('players.edit.gender') }}
                        </label>
                        <select class="block w-56 px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="gender" v-model="form.gender">
                            <option value="0">{{ t('global.male') }}</option>
                            <option value="1">{{ t('global.female') }}</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="px-3">
                        <button class="px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" type="submit">
                            {{ t('players.edit.update') }}
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Job -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.job.job') }}
                </h2>
            </template>

            <template>
                <div class="flex flex-wrap mb-4">
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3" for="job">
                            {{ t('players.job.name') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="job" v-model="form.job_name">
                            <option :value="job.name" v-for="job in jobs">{{ job.name || t('global.none') }}</option>
                        </select>
                    </div>
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3" v-if="form.job_name === job.name" v-for="job in jobs">
                        <label class="block mb-3" for="department">
                            {{ t('players.job.department') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="department" v-model="form.department_name">
                            <option :value="department.name" v-for="department in job.departments">{{ department.name || t('global.none') }}</option>
                        </select>
                    </div>
                    <template v-if="form.job_name === job.name" v-for="job in jobs">
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3" v-if="form.department_name === department.name" v-for="department in job.departments">
                            <label class="block mb-3" for="position">
                                {{ t('players.job.position') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="position" v-model="form.position_name">
                                <option :value="position" v-for="position in department.positions">{{ position || t('global.none') }}</option>
                            </select>
                        </div>
                    </template>
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3">&nbsp;</label>
                        <button class="block w-full px-4 py-3 mb-3 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" @click="updateJob">
                            {{ t('players.job.set') }}
                        </button>
                    </div>
                </div>
            </template>
        </v-section>

        <!-- Vehicle Editing -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isVehicleEdit">
            <div class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.vehicle.edit') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/3 text-center pt-2 font-bold" for="vehicleOwner">
                        {{ t('players.characters.vehicle.owner') }}
                    </label>
                    <input class="block w-2/3 px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="number" min="1" max="99999" id="vehicleOwner" v-model="vehicleForm.owner">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isVehicleEdit = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button" class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-success mr-3 dark:bg-dark-success" @click="editVehicle">
                        {{ t('players.characters.vehicle.confirm') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Vehicles -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.vehicles.vehicles') }}
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-9">
                    <card
                        :key="vehicle.id"
                        v-for="(vehicle) in character.vehicles"
                        class="relative"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ vehicle.model_name }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.vehicles.plate') }}:</span> {{ vehicle.plate }}
                            </h4>
                        </template>

                        <template>
                            <p v-html="t('players.vehicles.parked', vehicle.garage_name)">
                                {{ t('players.vehicles.parked', vehicle.garage_name) }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/vehicle/' + vehicle.id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-warning dark:bg-dark-warning rounded"
                                @click="startEditVehicle($event, vehicle)"
                                href="#"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-trash-alt mr-1"></i>
                                {{ t('players.characters.vehicle.confirm') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-red-600 dark:bg-red-400 rounded"
                                @click="deleteVehicle($event, vehicle.id)"
                                href="#"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-trash-alt mr-1"></i>
                                {{ t('global.delete') }}
                            </inertia-link>

                            <inertia-link
                                class="block px-2 py-1 text-center text-white absolute top-1 right-10 bg-blue-600 dark:bg-blue-400 rounded"
                                @click="findInventory($event)"
                                :href="'/inventory_find/trunk/' + vehicle.id"
                                :title="t('inventories.show_trunk')"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-car-side"></i>
                            </inertia-link>
                            <inertia-link
                                class="block px-2 py-1 text-center text-white absolute top-1 right-1 bg-blue-600 dark:bg-blue-400 rounded"
                                @click="findInventory($event)"
                                :href="'/inventory_find/glovebox/' + vehicle.id"
                                :title="t('inventories.show_glovebox')"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-car"></i>
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.vehicles.length === 0">
                    {{ t('players.vehicles.none') }}
                </p>
            </template>
        </v-section>

        <!-- Properties -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.properties.properties') }}
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        :key="property.id"
                        v-for="(property) in character.properties"
                        :no_body="true"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ property.property_address }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.cost') }}:</span> {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(property.property_cost) }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.rent') }}:</span> {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(property.property_income) }}
                            </h4>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 mt-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/property/' + property.property_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.properties.length === 0">
                    {{ t('players.properties.none') }}
                </p>
            </template>
        </v-section>

        <!-- Motels -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.motels.motels') }}
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        :key="motel.id"
                        v-for="(motel) in motels"
                        :no_body="true"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ motel.motel }} #{{ motel.room_id }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.motels.expires') }}:</span> {{ motel.expire | formatTime(true) }}
                            </h4>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="motels.length === 0">
                    {{ t('players.motels.none') }}
                </p>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../../Layouts/App';
import VSection from './../../../Components/Section';
import Card from './../../../Components/Card';
import Badge from './../../../Components/Badge';
import Modal from "../../../Components/Modal";
import Jobs from "../../../data/jobs.json";

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
        Badge,
        Modal,
    },
    props: {
        player: {
            type: Object,
            required: true,
        },
        character: {
            type: Object,
            required: true,
        },
        motels: {
            type: Array,
            required: true,
        },
        resetCoords: {
            type: Array,
            required: true,
        },
    },
    data() {
        let jobs = Jobs.sort((a, b) => {
            return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
        });

        for (let x = 0; x < jobs.length; x++) {
            let departments = jobs[x].departments.sort((a, b) => {
                return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
            });

            for (let y = 0; y < departments.length; y++) {
                departments[y].positions = departments[y].positions.reverse();
            }

            jobs[x].departments = departments;
        }

        jobs.unshift({
            name: null,
            departments: [
                {
                    name: null,
                    positions: [
                        null
                    ]
                }
            ]
        });

        return {
            local: {
                birth: this.t("players.edit.born", this.$moment(this.character.dateOfBirth).format('l')),
                cash: this.t("players.edit.cash", new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(this.character.money)),
                cashTitle: this.t(
                    "players.edit.cash_title",
                    new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(this.character.cash),
                    new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(this.character.bank)
                ),
                stocks: this.t("players.edit.stocks", new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(this.character.stocksBalance)),
            },
            form: {
                first_name: this.character.firstName,
                last_name: this.character.lastName,
                date_of_birth: this.character.dateOfBirth,
                gender: this.character.gender,
                backstory: this.character.backstory,
                job_name: this.character.jobName,
                department_name: this.character.departmentName,
                position_name: this.character.positionName,
            },
            location: window.location.href,
            vehicleForm: {
                id: 0,
                owner: 0
            },
            balanceForm: {
                cash: this.character.cash,
                bank: this.character.bank
            },
            isTattooRemoval: false,
            isResetSpawn: false,
            jobs: jobs,
            isVehicleEdit: false,
            isEditingBalance: false,
        };
    },
    methods: {
        submit(isJobUpdate) {
            let form = this.form,
                query = '';
            if (isJobUpdate) {
                form.first_name = this.character.firstName;
                form.last_name = this.character.lastName;
                form.date_of_birth = this.character.dateOfBirth;
                form.gender = this.character.gender;
                form.backstory = this.character.backstory;
                query = '?jobUpdate=yes';
            } else {
                form.job_name = this.character.jobName;
                form.department_name = this.character.departmentName;
                form.position_name = this.character.positionName;
            }

            this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + query, form)
        },
        async deleteVehicle(e, vehicleId) {
            e.preventDefault();

            if (!confirm(this.t('players.vehicles.delete_vehicle'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/vehicles/delete/' + vehicleId);
        },
        async findInventory(e) {
            e.preventDefault();

            try {
                const url = $(e.target).attr('href') + '?json=yes'
                const data = await axios.get(url);

                if (data.data && 'error' in data.data && data.data.error) {
                    alert(data.data.error);
                } else if (data.data && 'redirect' in data.data && data.data.redirect) {
                    window.location.href = data.data.redirect;
                }
            } catch (e) {
            }
        },
        sortJobs(array, type) {
            switch(type) {
                case 'job':
                case 'department':
                    array.sort((a, b) => {
                        return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
                    });
                    return array;
                case 'position':
                    array.sort((a, b) => {
                        return a.toLowerCase() < b.toLowerCase() ? -1 : 1
                    });
                    return array;
            }

            return [];
        },
        updateJob() {
            this.submit(true);
        },
        startEditVehicle(e, vehicle) {
            e.preventDefault();

            this.vehicleForm.id = vehicle.id;
            this.vehicleForm.owner = vehicle.owner_cid;
            this.isVehicleEdit = true;
        },
        async removeTattoos() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/removeTattoos', {
                zone: $('#zone').val(),
            });

            // Reset.
            this.isTattooRemoval = false;
        },
        async resetSpawn() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/resetSpawn', {
                spawn: $('#spawn').val(),
            });

            // Reset.
            this.isResetSpawn = false;
        },
        async editVehicle() {
            // Send request.
            await this.$inertia.post('/vehicles/edit/' + this.vehicleForm.id, this.vehicleForm);

            // Reset.
            this.isVehicleEdit = false;
        },
        async editBalance() {
            // Send request.
            await this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/editBalance', this.balanceForm);

            this.local.cash = this.t("players.edit.cash", new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(this.character.money));

            // Reset.
            this.isEditingBalance = false;
        },
    },
}
</script>
