<template>
    <div>

        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1 class="dark:text-white">
                    {{ character.name }} #{{ character.id }}
                </h1>
                <div class="flex items-center space-x-5">
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
                    <badge class="bg-gray-100 border-gray-200 dark:bg-dark-secondary" :title="local.cashTitle" v-html="local.cash">
                        {{ local.cash }}
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
                <a href="#" class="px-5 py-2 font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger" @click="function(e) {e.preventDefault(); isTattooRemoval = true}">
                    <i class="fas fa-eraser"></i>
                    {{ t('players.characters.remove_tattoo') }}
                </a>
                <!-- Back -->
                <a class="px-5 py-2 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500" :href="'/players/' + player.steamIdentifier">
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
                <div class="flex justify-end">
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isTattooRemoval = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button" class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger" @click="removeTattoos">
                        {{ t('players.characters.tattoo_do') }}
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
                <form @submit.prevent="submit">
                    <!-- Name -->
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="first_name">
                                {{ t('players.edit.prename') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="first_name" v-model="form.first_name">
                        </div>
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="last_name">
                                {{ t('players.edit.surname') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="last_name" v-model="form.last_name">
                        </div>
                        <div class="w-1/3 px-3">
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
                            Gender
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
            <div class="flex items-center justify-between mx-3 space-x-6">
                <h2 class="mr-12 text-2xl font-semibold">
                    {{ t('players.job.job') }}
                </h2>
                <div class="flex items-center justify-center flex-1 space-x-4">
                    <p class="text-xl">
                        <span class="font-semibold">{{ t('players.job.name') }}:</span> {{ character.jobName || t('global.none') }}
                    </p>
                    <button @click.prevent="resetJobName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button">
                        {{ t('players.job.reset') }}
                    </button>
                </div>
                <div class="flex items-center justify-center flex-1 space-x-6">
                    <p class="text-xl">
                        <span class="font-semibold">{{ t('players.job.department') }}:</span> {{ character.departmentName || t('global.none') }}
                    </p>
                    <button @click.prevent="resetDepartmentName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button">
                        {{ t('players.job.reset') }}
                    </button>
                </div>
                <div class="flex items-center justify-center flex-1 space-x-6">
                    <p class="text-xl">
                        <span class="font-semibold">{{ t('players.job.position') }}:</span> {{ character.positionName || t('global.none') }}
                    </p>
                    <button @click.prevent="resetPositionName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button">
                        {{ t('players.job.reset') }}
                    </button>
                </div>
            </div>
        </v-section>

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
                <p class="text-muted dark:text-dark-muted" v-if="character.properties.length === 0">
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
    },
    data() {
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
            isTattooRemoval: false,
        };
    },
    methods: {
        submit() {
            this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id, this.form)
        },
        resetJobName() {
            this.form.job_name = null;
            this.form.department_name = null;
            this.form.position_name = null;
            this.submit();
        },
        resetDepartmentName() {
            this.form.department_name = null;
            this.form.position_name = null;
            this.submit();
        },
        resetPositionName() {
            this.form.position_name = null;
            this.submit();
        },
        async removeTattoos() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/removeTattoos', {
                zone: $('#zone').val(),
            });

            // Reset.
            this.isTattooRemoval = false;
        },
    },
}
</script>
