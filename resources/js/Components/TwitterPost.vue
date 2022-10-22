<template>
    <div class="flex pt-3 pb-3 border-t w-full border-gray-400 dark:border-gray-500 px-2 relative hover:bg-gray-100 dark:hover:bg-gray-700">
        <div v-if="dontLink" class="mr-2 flex-shrink-0">
            <img
                class="block w-12 h-12 rounded-full"
                :src="user.avatar_url"
                @error="avatarError"
            />
            <span class="block text-xs text-center mt-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-heart text-red-800 dark:text-red-500"></i> {{ post.likes }}
            </span>
        </div>
        <inertia-link class="block mr-2 flex-shrink-0" :href="'/twitter/' + post.authorId" v-else>
            <img
                class="block w-12 h-12 rounded-full"
                :src="user.avatar_url"
                @error="avatarError"
            />
            <span class="block text-xs text-center mt-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-heart text-red-600 dark:text-red-500"></i> {{ post.likes }}
            </span>
        </inertia-link>

        <div>
            <div v-if="dontLink" class="block mb-2 font-bold">
                {{ user.username }}
                <span :title="post.time | formatTime(true)"
                      class="text-gray-400 dark:text-gray-500 font-normal">- {{ formatDate(post.time) }}</span>
            </div>
            <inertia-link :href="'/twitter/' + post.authorId" class="block mb-2 font-bold" v-else>
                <span class="hover:underline">{{ user.username }}</span>
                <span :title="post.time | formatTime(true)"
                      class="text-gray-400 dark:text-gray-500 font-normal">- {{ formatDate(post.time) }}</span>
            </inertia-link>

            <div class="text-sm block" v-html="formatBody(post.message)"></div>
        </div>

        <inertia-link
            class="block px-1.5 py-1 text-center text-white text-xs absolute top-1 right-1 bg-red-600 dark:bg-red-400 rounded"
            href="#"
            @click="deleteTweet($event, post.id)"
            v-if="$page.auth.player.isSuperAdmin"
        >
            <i class="fas fa-trash-alt"></i>
        </inertia-link>
    </div>
</template>
<script>
export default {
    name: 'TwitterPost',
    props: {
        post: {
            type: Object,
            required: true
        },
        user: {
            type: Object,
            required: true
        },
        dontLink: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        formatDate(date) {
            const d = this.$moment.utc(date).local(),
                day = d.format('DD-MM-YYYY'),
                today = this.$moment().format('DD-MM-YYYY'),
                yesterday = this.$moment().subtract(1, 'days').format('DD-MM-YYYY'),
                time = d.format('h:mm A');

            if (day === today) {
                return 'Today at ' + time;
            } else if (day === yesterday) {
                return 'Yesterday at ' + time;
            }

            return d.format('MM/DD/YYYY');
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
        formatBody(body) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return body.replace(urlRegex, function (url) {
                const tmp = url.split('?')[0].toLowerCase().split('.'),
                    ext = tmp[tmp.length - 1];

                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        return '<div class="max-w-screen-sm"><img src="' + url + '" class="block max-w-full max-h-img" /></div>';
                }

                return '<a href="' + url + '" class="text-indigo-600 dark:text-indigo-400" target="_blank">' + url + '</a>';
            })
        }
    }
}
</script>
