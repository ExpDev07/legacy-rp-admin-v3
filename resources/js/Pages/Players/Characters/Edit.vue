<template>
    <div>

        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1>
                    {{ character.name }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="bg-gray-100 border-gray-200">
                        <span class="font-semibold">{{ character.gender }}</span>
                    </badge>
                    <badge class="bg-gray-100 border-gray-200">
                        Born on <span class="font-semibold">{{ $moment(character.dateOfBirth).format('l') }}</span>
                    </badge>
                    <badge class="bg-gray-100 border-gray-200">
                        <span class="font-semibold">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(character.money) }}</span> in cash/bank
                    </badge>
                    <badge class="bg-gray-100 border-gray-200">
                        <span class="font-semibold">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(character.stocksBalance) }}</span> in stocks
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
                        <div class="w-1/2 px-3">
                            <label class="block mb-2" for="first_name">
                                First Name
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded" id="first_name" v-model="form.first_name">
                        </div>
                        <div class="w-1/2 px-3">
                            <label class="block mb-2" for="last_name">
                                Last Name
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded" id="last_name" v-model="form.last_name">
                        </div>
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="backstory">
                            Backstory
                        </label>
                        <textarea class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded" id="backstory" v-model="form.backstory"></textarea>
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="dob">
                            Date of Birth
                        </label>
                        <input class="block w-56 px-4 py-3 mb-3 bg-gray-200 border rounded" id="dob" v-model="form.date_of_birth">
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3" for="gender">
                            Gender
                        </label>
                        <select class="block w-56 px-4 py-3 mb-3 bg-gray-200 border rounded" id="gender" v-model="form.gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="px-3">
                        <button class="px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded" type="submit">
                            Update Character
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Job -->
        <v-section>
            <div class="flex items-center justify-between mx-3 space-x-6">
                <h2 class="mr-12 text-2xl font-semibold">
                    Job
                </h2>
                <div class="flex items-center justify-center flex-1 space-x-4">
                    <p class="text-xl">
                        <span class="font-semibold">Name:</span> {{ character.jobName || 'None' }}
                    </p>
                    <button @click.prevent="resetJobName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded" type="button">
                        Reset
                    </button>
                </div>
                <div class="flex items-center justify-center flex-1 space-x-6">
                    <p class="text-xl">
                        <span class="font-semibold">Department:</span> {{ character.departmentName || 'None' }}
                    </p>
                    <button @click.prevent="resetDepartmentName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded" type="button">
                        Reset
                    </button>
                </div>
                <div class="flex items-center justify-center flex-1 space-x-6">
                    <p class="text-xl">
                        <span class="font-semibold">Position:</span> {{ character.positionName || 'None' }}
                    </p>
                    <button @click.prevent="resetPositionName" class="px-6 py-1 font-semibold text-center text-white bg-indigo-600 rounded" type="button">
                        Reset
                    </button>
                </div>
            </div>
        </v-section>

        <!-- Vehicles -->
        <v-section>
            <template #header>
                <h2>
                    Vehicles
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
                            <h4 class="text-primary">
                                <span>Plate number:</span> {{ vehicle.plate }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                Parked at <span class="italic">{{ vehicle.garage_name }}</span>.
                            </p>
                        </template>
                    </card>
                </div>
                <p class="text-muted" v-if="character.vehicles.length === 0">
                    This character does not own any vehicles.
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
