<template>
    <div>
        <h1 class="text-3xl font-bold mb-12">
            {{ character.name }}
        </h1>

        <!-- Editing -->
        <div class="rounded bg-gray-100 px-5 py-6 mb-8">
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

        <!-- Editing -->
        <div class="rounded bg-gray-100 px-5 py-6 mb-8">
            <h2 class="text-2xl font-semibold mx-3 mb-8">
                Job
            </h2>
        </div>

        <!-- Vehicles -->
        <div class="rounded bg-gray-100 p-5 mb-8">
            <h2 class="text-2xl font-semibold mx-3 mb-8">
                Vehicles
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div class="p-5 bg-gray-200" :key="vehicle.id" v-for="vehicle in character.vehicles">
                    <h1 class="text-lg font-semibold mb-2">
                        {{ vehicle.model_name }} ({{ vehicle.plate }})
                    </h1>
                    <h2>
                        Parked at {{ vehicle.garage_name }}.
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
