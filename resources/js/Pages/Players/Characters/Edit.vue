<template>
    <div>
        <h1 class="text-3xl font-bold mb-8">
            {{ character.name }}
        </h1>

        <!-- Statistics -->
        <div class="flex text-center mb-8">
            <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
                <h1 class="font-semibold text-xl mb-3">
                    Date of Birth
                </h1>
                <p>
                    {{ new Date(character.dateOfBirth).toLocaleString() }}
                </p>
            </div>
            <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
                <h1 class="font-semibold text-xl mb-3">
                    Money
                </h1>
                <p>
                    ${{ character.money }}
                </p>
            </div>
            <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
                <h1 class="font-semibold text-xl mb-3">
                    Job Title
                </h1>
                <p>
                    {{ character.jobName }}
                </p>
            </div>
        </div>

        <!-- Editing -->
        <div class="rounded bg-gray-300 px-5 py-6 mb-8">
            <form class="w-full" @submit.prevent="submit">
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
                <div class="w-full px-3 mb-6">
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

        <!-- Vehicles -->
        <div class="rounded bg-gray-300 p-5 mb-8">
            <h2 class="text-2xl mx-3 mb-3">
                Owned vehicles
            </h2>
            <div class="grid grid-cols-3 xl:grid-cols-4">
                <div class="flex flex-col bg-white shadow rounded p-5 m-3" v-for="vehicle in character.vehicles" v-bind:key="vehicle.id">
                    <div class="flex-grow">
                        <div class="text-center border-b mb-5 pb-4">
                            <h1 class="text-xl font-bold mb-2">
                                {{ vehicle.model_name }}
                            </h1>
                            <h3 class="text-lg text-indigo-500">
                                <span class="font-semibold">Plate Number:</span> {{ vehicle.plate }}
                            </h3>
                        </div>
                        <p class="mb-8">
                            This vehicle is currently parked at <span class="font-semibold">{{ vehicle.garage_name }}</span>.
                        </p>
                    </div>
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
