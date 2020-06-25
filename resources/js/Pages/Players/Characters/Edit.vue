<template>
<div>
    <h1 class="text-3xl font-bold mb-8">
        {{ character.name }}
    </h1>

    <!-- Statistics -->
    <div class="flex text-center mb-8">
        <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
            <span class="font-semibold">DOB:</span> {{ new Date(character.dateOfBirth).toLocaleString() }}
        </div>
        <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
            <span class="font-semibold">Money:</span> {{ character.money }}
        </div>
        <div class="w-full rounded shadow bg-gray-800 text-white px-5 py-6 m-3">
            <span class="font-semibold">Job Title:</span> {{ character.jobName }}
        </div>
    </div>

    <!-- Editing -->
    <div class="rounded bg-gray-300 px-5 py-6">
        <form class="w-full" @submit.prevent="submit">
            <!-- Name -->
            <div class="flex flex-wrap mb-4">
                <div class="w-1/2 px-3 mb-6">
                    <label class="block mb-2" for="firstname">
                        First Name
                    </label>
                    <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="first_name" v-model="form.first_name">
                </div>
                <div class="w-1/2 px-3 mb-0">
                    <label class="block mb-2" for="lastname">
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
