<template>
    <div>

        <template>
            <div class="w-full items-center flex flex-wrap mb-6 max-w-screen-md m-auto">
                <div class="mr-3">
                    <img
                        class="block w-24 h-24 rounded-full"
                        :src="user.avatar_url"
                        @error="avatarError"
                    />
                </div>
                <div>
                    <inertia-link :href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'" class="hover:underline text-xl dark:text-white">
                        {{ character.name }}
                        <sup>{{ character.id }}</sup>
                    </inertia-link>
                    <h3 class="text-base text-gray-500 dark:text-gray-400">
                        @{{ user.username }} -
                        <inertia-link :href="'/players/' + player.steamIdentifier" class="hover:underline">{{ player.playerName }}</inertia-link>
                    </h3>
                </div>
            </div>
        </template>

        <template>
            <div class="w-full flex flex-wrap max-w-screen-md m-auto">
                <TwitterPost v-for="post in tweets" :key="post.id" :post="post" :user="user" :dont-link="true" />
            </div>
        </template>

        <template>
            <div class="flex items-center justify-between mt-6 mb-1">

                <!-- Navigation -->
                <div class="flex flex-wrap">
                    <inertia-link
                        class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                        :href="links.prev"
                        v-if="page >= 2"
                    >
                        <i class="mr-1 fas fa-arrow-left"></i>
                        {{ t("pagination.previous") }}
                    </inertia-link>
                    <inertia-link
                        class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                        v-if="tweets.length === 15"
                        :href="links.next"
                    >
                        {{ t("pagination.next") }}
                        <i class="ml-1 fas fa-arrow-right"></i>
                    </inertia-link>
                </div>

                <!-- Meta -->
                <div class="font-semibold">
                    {{ t("pagination.page", page) }}
                </div>

            </div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from "../../Components/Pagination";
import TwitterPost from "../../Components/TwitterPost";

export default {
    layout: Layout,
    components: {
        VSection,
        Pagination,
        TwitterPost,
    },
    methods: {
        avatarError(e) {
            // Replace with default
            e.target.src = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wgARCAAgACADASIAAhEBAxEB/8QAFwABAQEBAAAAAAAAAAAAAAAABAcABv/EABYBAQEBAAAAAAAAAAAAAAAAAAABBf/aAAwDAQACEAMQAAAByyULWyeIJQp6akTd0vdTdwT/xAAaEAEBAAMBAQAAAAAAAAAAAAADBAECEAAT/9oACAEBAAEFAvTgj7UAgbcjL4z2Fhp+DvhCbfBlyepQ9RUr8//EABgRAAIDAAAAAAAAAAAAAAAAAAADARMh/9oACAEDAQE/AUKsnR6q5wQ2udHtsnD/xAAYEQACAwAAAAAAAAAAAAAAAAAAAwETIf/aAAgBAgEBPwF7a4wQ2yNHqsjBCq40/8QAHxAAAgECBwAAAAAAAAAAAAAAAQIAEDEDESEiQVGB/9oACAEBAAY/Aplhj2ZYg9qi83MdebiqMLER2NgK7Dp0ZvOnQp//xAAaEAEAAgMBAAAAAAAAAAAAAAABABEQITFx/9oACAEBAAE/IYqs11aCCqN8GxyAhx6LEIceCZYvQYxdIc2Id9pWShDrsKjH/9oADAMBAAIAAwAAABDzzzz/xAAZEQEBAAMBAAAAAAAAAAAAAAABABEhMUH/2gAIAQMBAT8QBzggM4Y28MZYaL//xAAZEQEBAAMBAAAAAAAAAAAAAAABABEhMUH/2gAIAQIBAT8QQ3pkd6Jy9E7Zbb//xAAfEAEAAQMFAQEAAAAAAAAAAAABEQAxURAhQYHBYXH/2gAIAQEAAT8QoUw3t+OXy9CmG9v1w+aLAuKOcIvhSvnVGOQ3wpHzuhkHNNmL0fZGRwxCdMlH23M5YgO2CixN9CVj3E5Dh/KJGNcTlOX90//Z';
        }
    },
    props: {
        tweets: {
            type: Array,
            required: true,
        },
        user: {
            type: Object,
            required: true,
        },
        character: {
            type: Object,
            required: true,
        },
        player: {
            type: Object,
            required: true,
        },
        links: {
            type: Object,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        }
    },
}
</script>
