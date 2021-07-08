<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('twitter.title') }}
            </h1>
            <p>
                {{ t('twitter.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('global.refresh') }}
            </button>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('twitter.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Details -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-3" for="username">
                                {{ t('twitter.account') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="username" :placeholder="t('twitter.placeholder_username')" v-model="filters.username">
                        </div>
                        <!-- Details -->
                        <div class="w-2/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-3" for="message">
                                {{ t('twitter.message') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="message" :placeholder="t('twitter.placeholder_message')" v-model="filters.message">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">* {{ t('global.search.exact') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                    </div>
                    <!-- Search button -->
                    <div class="w-full px-3 mt-3">
                        <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('twitter.search') }}
                            </span>
                            <span v-else>
                                <i class="fas fa-cog animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('twitter.posts') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4"></th>
                        <th class="px-6 py-4">{{ t('twitter.account') }}</th>
                        <th class="px-6 py-4">{{ t('twitter.message') }}</th>
                        <th class="px-6 py-4">{{ t('twitter.likes') }}</th>
                        <th class="px-6 py-4">{{ t('twitter.time') }}</th>
                        <th class="px-6 py-4"></th>
                        <th class="px-6 py-4" v-if="$page.auth.player.isSuperAdmin"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="post in posts" :key="post.id">
                        <td class="px-6 py-3 border-t mobile:block w-90 mobile:w-full" v-if="user(post.authorId)">
                            <img
                                class="inline-block w-8 h-8 rounded-full ring-2 ring-white"
                                :src="user(post.authorId).avatarUrl"
                                @error="avatarError"
                            />
                        </td>
                        <td class="px-6 py-3 border-t mobile:hidden" v-else></td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="user(post.authorId)">
                            <span class="font-semibold" v-if="user(post.authorId).username.length <= 18">{{ user(post.authorId).username }}</span>
                            <span class="font-semibold" :title="user(post.authorId).username" v-else>{{ user(post.authorId).username.substring(0, 15) + '...' }}</span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:hidden" v-else></td>
                        <td class="px-6 py-3 border-t max-w-2xl mobile:block" v-linkified:options="{ className: 'text-indigo-600 dark:text-indigo-400' }">{{ post.message }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ post.likes }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ post.time | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="character(post.realUser)">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + character(post.realUser).steam_identifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:hidden" v-else></td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="$page.auth.player.isSuperAdmin">
                            <inertia-link
                                class="block px-4 py-2 font-semibold text-center text-white bg-red-600 dark:bg-red-400 rounded"
                                href="#"
                                @click="deleteTweet($event, post.id)"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="posts.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('twitter.no_posts') }}
                        </td>
                    </tr>
                </table>
            </template>

            <template #footer>
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
                            v-if="posts.length === 15"
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
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from "../../Components/Pagination";

export default {
    layout: Layout,
    components: {
        VSection,
        Pagination,
    },
    props: {
        posts: {
            type: Array,
            required: true,
        },
        filters: {
            message: String,
        },
        characterMap: {
            type: Object,
            required: true,
        },
        userMap: {
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
        },
        time: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false
        };
    },
    methods: {
        character(id) {
            return id in this.characterMap ? this.characterMap[id] : null;
        },
        user(id) {
            return id in this.userMap ? this.userMap[id] : null;
        },
        avatarError(e) {
            // Replace with default
            e.target.src = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wgARCAAgACADASIAAhEBAxEB/8QAFwABAQEBAAAAAAAAAAAAAAAABAcABv/EABYBAQEBAAAAAAAAAAAAAAAAAAABBf/aAAwDAQACEAMQAAAByyULWyeIJQp6akTd0vdTdwT/xAAaEAEBAAMBAQAAAAAAAAAAAAADBAECEAAT/9oACAEBAAEFAvTgj7UAgbcjL4z2Fhp+DvhCbfBlyepQ9RUr8//EABgRAAIDAAAAAAAAAAAAAAAAAAADARMh/9oACAEDAQE/AUKsnR6q5wQ2udHtsnD/xAAYEQACAwAAAAAAAAAAAAAAAAAAAwETIf/aAAgBAgEBPwF7a4wQ2yNHqsjBCq40/8QAHxAAAgECBwAAAAAAAAAAAAAAAQIAEDEDESEiQVGB/9oACAEBAAY/Aplhj2ZYg9qi83MdebiqMLER2NgK7Dp0ZvOnQp//xAAaEAEAAgMBAAAAAAAAAAAAAAABABEQITFx/9oACAEBAAE/IYqs11aCCqN8GxyAhx6LEIceCZYvQYxdIc2Id9pWShDrsKjH/9oADAMBAAIAAwAAABDzzzz/xAAZEQEBAAMBAAAAAAAAAAAAAAABABEhMUH/2gAIAQMBAT8QBzggM4Y28MZYaL//xAAZEQEBAAMBAAAAAAAAAAAAAAABABEhMUH/2gAIAQIBAT8QQ3pkd6Jy9E7Zbb//xAAfEAEAAQMFAQEAAAAAAAAAAAABEQAxURAhQYHBYXH/2gAIAQEAAT8QoUw3t+OXy9CmG9v1w+aLAuKOcIvhSvnVGOQ3wpHzuhkHNNmL0fZGRwxCdMlH23M5YgO2CixN9CVj3E5Dh/KJGNcTlOX90//Z';
        },
        async deleteTweet(e, tweetId) {
            e.preventDefault();

            if (!confirm(this.t('twitter.delete_tweet'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/tweets/delete/' + tweetId);
        },
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/twitter', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'posts', 'userMap', 'characterMap', 'time', 'links', 'page' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
    },
}
</script>
