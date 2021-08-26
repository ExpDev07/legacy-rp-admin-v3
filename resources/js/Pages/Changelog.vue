<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("changelog.title") }}
            </h1>
            <p>
                {{ t("changelog.description") }}
            </p>
        </portal>

        <div class="-mt-6">
            <div v-for="update in updates" :key="update.title" class="pb-3 mb-3 border-b-2 border-gray-800 dark:border-gray-500">
                <h2>{{ update.title }}</h2>
                <p class="text-sm text-muted dark:text-dark-muted mb-2">{{ update.time | formatTime }}</p>
                <ul>
                    <li v-for="li in update.body" :key="li" class="ml-3 pl-2 list-dash">
                        {{ li }}
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
import Layout from './../Layouts/App';

export default {
    layout: Layout,
    props: {
        updates: {
            type: Array,
            required: true
        }
    },
}
</script>
