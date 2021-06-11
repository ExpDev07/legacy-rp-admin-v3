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

        <!-- Editing -->
        <v-section>
            <template #header>
                <h2>
                    Info
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
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.vehicles.length === 0">
                    {{ t('players.vehicles.none') }}
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

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
        Badge,
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
        }
    },
}
</script>
