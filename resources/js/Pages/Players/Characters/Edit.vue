<template>
    <div>
        <div class="prose max-w-full mb-12">
            <div class="flex items-start space-x-10">
                <h1>
                    {{ character.name }}
                </h1>
                <div class="flex items-center space-x-5">
                    <div class="px-5 py-1 rounded bg-gray-100 border-2 border-gray-200">
                        <span class="font-semibold">{{ character.gender }}</span>
                    </div>
                    <div class="px-5 py-1 rounded bg-gray-100 border-2 border-gray-200">
                        Born on <span class="font-semibold">{{ $moment(character.dateOfBirth).format('l') }}</span>
                    </div>
                    <div class="px-5 py-1 rounded bg-gray-100 border-2 border-gray-200">
                        <span class="font-semibold">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(character.money) }}</span> in cash/bank
                    </div>
                    <div class="px-5 py-1 rounded bg-gray-100 border-2 border-gray-200">
                        <span class="font-semibold">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(character.stocksBalance) }}</span> in stocks
                    </div>
                </div>
            </div>
        </div>

        <!-- Editing -->
        <div class="rounded bg-gray-100 p-8 mb-8">
            <h2 class="text-2xl font-semibold mx-3 mb-8">
                Update
            </h2>
            <form @submit.prevent="submit">
                <!-- Name -->
                <div class="flex flex-wrap mb-4">
                    <div class="w-1/2 px-3 mb-6">
                        <label class="block mb-2" for="first_name">
                            First Name
                        </label>
                        <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="first_name" v-model="form.first_name">
                    </div>
                    <div class="w-1/2 px-3 mb-0">
                        <label class="block mb-2" for="last_name">
                            Last Name
                        </label>
                        <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="last_name" v-model="form.last_name">
                    </div>
                </div>
                <!-- Backstory -->
                <div class="px-3 mb-6">
                    <label class="block mb-3" for="backstory">
                        Backstory
                    </label>
                    <textarea class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="backstory" v-model="form.backstory"></textarea>
                </div>

                <!-- Submit -->
                <div class="px-3">
                    <button class="bg-indigo-600 font-semibold text-white text-center rounded px-4 py-2" type="submit">
                        Update Character
                    </button>
                </div>
            </form>
        </div>

        <!-- Job -->
        <div class="rounded bg-gray-100 p-8 mb-8">
            <div class="flex items-center justify-between space-x-6 mx-3">
                <h2 class="text-2xl font-semibold mr-12">
                    Job
                </h2>
                <div class="flex-1 flex items-center justify-center space-x-4">
                    <h3 class="text-xl">
                        <span class="font-semibold">Name:</span> {{ character.jobName || 'None' }}
                    </h3>
                    <button @click.prevent="resetJobName" class="bg-indigo-600 font-semibold text-white text-center rounded px-6 py-1" type="button">
                        Reset
                    </button>
                </div>
                <div class="flex-1 flex items-center justify-center space-x-6">
                    <h3 class="text-xl">
                        <span class="font-semibold">Department:</span> {{ character.departmentName || 'None' }}
                    </h3>
                    <button @click.prevent="resetDepartmentName" class="bg-indigo-600 font-semibold text-white text-center rounded px-6 py-1" type="button">
                        Reset
                    </button>
                </div>
                <div class="flex-1 flex items-center justify-center space-x-6">
                    <h3 class="text-xl">
                        <span class="font-semibold">Position:</span> {{ character.positionName || 'None' }}
                    </h3>
                    <button @click.prevent="resetPositionName" class="bg-indigo-600 font-semibold text-white text-center rounded px-6 py-1" type="button">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Vehicles -->
        <div class="rounded bg-gray-100 p-8 mb-8">
            <h2 class="text-2xl font-semibold mx-3 mb-8">
                Vehicles
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div class="p-8 bg-gray-200" :key="vehicle.id" v-for="vehicle in character.vehicles">
                    <h1 class="text-lg font-semibold mb-3">
                        {{ vehicle.model_name }} ({{ vehicle.plate }})
                    </h1>
                    <h2>
                        Parked at <span class="italic">{{ vehicle.garage_name }}</span>.
                    </h2>
                </div>
            </div>
            <p class="px-4 py-6" v-if="character.vehicles.length === 0">
                This character does not own any vehicles.
            </p>
        </div>

    </div>
</template>

<script>
import Layout from './../../../Layouts/App';

export default {
    layout: Layout,
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
};
</script>
