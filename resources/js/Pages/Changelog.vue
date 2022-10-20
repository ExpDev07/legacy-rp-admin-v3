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
            <div v-for="update in updates.updates" :key="update.title" class="pb-3 mb-3 border-b-2 border-gray-800 dark:border-gray-500" :class="{ 'opacity-40' : updates.current < update.id }">
                <h2 :class="{ 'text-green-500' : updates.current === update.id }">
                    {{ update.title }}
                    <sup v-if="updates.current === update.id" class="text-sm">
                        {{ t('changelog.current') }}
                    </sup>
                    <sup v-else-if="updates.current < update.id" class="text-sm">
                        {{ t('changelog.soon') }}
                    </sup>
                </h2>
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
            type: Object,
            required: true
        }
    },
}
</script>
