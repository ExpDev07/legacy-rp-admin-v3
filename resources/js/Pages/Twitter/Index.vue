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
        <v-section :noFooter="true">
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

        <template>
            <h2 class="mb-4 max-w-screen-md m-auto text-2xl">{{ t('twitter.title') }}</h2>

            <div class="w-full flex flex-wrap max-w-screen-md m-auto">
                <TwitterPost v-for="post in posts" :key="post.id" :post="post" :user="user(post.authorId)" :selectionChange="selectPost" />

                <div class="mt-3" v-if="selectedPosts.length > 0">
                    <button class="px-5 py-2 font-semibold text-white bg-danger dark:bg-dark-danger rounded hover:shadow-lg" @click="deleteSelected">
                        <i class="fas fa-trash"></i>
                        {{ t('twitter.delete_selected') }}
                    </button>
                </div>
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
    props: {
        posts: {
            type: Array,
            required: true,
        },
        filters: {
            message: String,
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
            isLoading: false,

            selectedPosts: []
        };
    },
    methods: {
        selectPost($event, id) {
            if ($event.target.checked) {
                this.selectedPosts.push(id);
            } else {
                this.selectedPosts = this.selectedPosts.filter(postId => postId !== id);
            }
        },
        async deleteSelected() {
            if (this.isLoading) {
                return;
            }

            if (!confirm(this.t('twitter.delete_selected_confirm'))) {
                return;
            }

            this.isLoading = true;

            try {
                await this.$inertia.post('/tweets/delete', {
                    ids: this.selectedPosts,
                }, {
                    preserveState: true,
                    preserveScroll: true
                });

                this.selectedPosts = [];
            } catch (e) {}

            this.isLoading = false;
        },
        user(id) {
            return id in this.userMap ? this.userMap[id] : null;
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
                    only: [ 'posts', 'userMap', 'time', 'links', 'page' ],
                });

                this.selectedPosts = [];
            } catch(e) {}

            this.isLoading = false;
        },
    },
}
</script>
