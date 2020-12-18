<template>
    <div>
        <div class="prose mb-12">
            <h1>
                {{ character.name }}
            </h1>
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
                    <button class="bg-indigo-600 hover:bg-orange-500 text-white text-center rounded px-4 py-2" type="submit">
                        Update Character
                    </button>
                </div>
            </form>
        </div>

        <!-- Job -->
        <div class="rounded bg-gray-100 p-8 mb-8">
            <div class="flex items-center justify-between text-center mx-3">
                <h2 class="text-2xl font-semibold mr-12">
                    Job
                </h2>
                <h3 class="flex-1 text-xl">
                    <span>Name:</span> {{ character.jobName }}
                </h3>
                <h3 class="flex-1 text-xl">
                    <span>Department:</span> {{ character.jobDepartment }}
                </h3>
                <h3 class="flex-1 text-xl">
                    <span>Position:</span> {{ character.jobPosition }}
                </h3>
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
            },
        };
    },
    methods: {
        submit: function () {
            this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id, this.form)
        },
    },
};
</script>
