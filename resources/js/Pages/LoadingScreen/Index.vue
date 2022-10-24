<template>
    <div>
        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('loading_screen.title') }}
            </h1>
            <p>
                {{ t('loading_screen.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded dark:bg-green-400 mr-1"
                type="button" @click="isAdding = true">
                <i class="mr-1 fa fa-plus"></i>
                {{ t('loading_screen.add') }}
            </button>

            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                type="button" @click="refresh">
                <span v-if="!isLoading">
                    <i class="mr-1 fa fa-refresh"></i>
                    {{ t('logs.refresh') }}
                </span>
                <span v-else>
                    <i class="fas fa-cog animate-spin"></i>
                    {{ t('global.loading') }}
                </span>
            </button>
        </portal>

        <template>
            <h2 class="mb-4 max-w-screen-md m-auto text-2xl">
                {{ t('loading_screen.pictures') }}
                <sup>{{ pictures.length }}</sup>
            </h2>

            <div v-if="loadedCount < pictures.length" class="badge px-5 py-1 border-2 max-w-screen-md m-auto mb-3 rounded border-gray-200 bg-gray-100 dark:bg-gray-700">
                {{ t("loading_screen.loading_images", loadedCount, pictures.length) }}
            </div>

            <div v-if="failedLoadCount > 0" class="badge px-5 py-1 border-2 max-w-screen-md m-auto mb-3 rounded border-red-200 bg-danger-pale dark:bg-dark-danger-pale">
                {{ t("loading_screen.failed_count", failedLoadCount, pictures.length) }}
            </div>

            <div v-if="smallSizeCount > 0" class="badge px-5 py-1 border-2 max-w-screen-md m-auto mb-3 rounded border-red-200 bg-danger-pale dark:bg-dark-danger-pale">
                {{ t("loading_screen.small_size_count", smallSizeCount, pictures.length) }}
            </div>

            <div class="w-full flex flex-wrap max-w-screen-md m-auto">
                <div class="flex pt-3 pb-3 border-t w-full border-gray-400 dark:border-gray-500 px-2 relative hover:bg-gray-100 dark:hover:bg-gray-700" v-for="(picture, index) in pictures" :key="picture.id">
                    <div>
                        <a clas="block relative" target="_blank" :href="picture.image_url">
                            <img :src="picture.image_url" class="h-48 border-red-500" @load="imageLoaded($event, picture.id)" @error="imageFailed(picture.id)" :class="{'border-4' : failedLoad[picture.id] || smallSize[picture.id]}" />

                            <span v-if="failedLoad[picture.id]" class="block text-sm text-red-400 mt-2 italic">
                                <i class="fas fa-skull-crossbones"></i>
                                {{ t("loading_screen.failed_count_label") }}
                            </span>
                            <span v-else-if="smallSize[picture.id]" class="block text-sm text-red-400 mt-2 italic">
                                <i class="fas fa-search-minus"></i>
                                {{ t("loading_screen.small_size_count_label", smallSize[picture.id]) }}
                            </span>
                        </a>
                    </div>

                    <inertia-link
                        class="block px-1.5 py-1 text-center text-white text-xs absolute top-1 right-1 bg-red-600 dark:bg-red-400 rounded"
                        href="#"
                        @click="deletePicture($event, picture.id)"
                        :title="t('loading_screen.remove')"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </inertia-link>
                </div>

                <img v-if="pictures.length === 0" src="/images/no-pictures.png" class="w-full" />
            </div>
        </template>

        <modal :show.sync="isAdding">
            <template #header>
                <h1 class="dark:text-white">
                    {{ t('loading_screen.add') }}
                </h1>
            </template>

            <template #default>
                <div>
                    <label class="block mb-3" for="url">
                        {{ t('loading_screen.picture') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="url" id="url" placeholder="https://images.unsplash.com/photo-1511044568932-338cba0ad803" v-model="image_url" required>
                </div>
            </template>

            <template #actions>
                <button type="button" class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400" @click="isAdding = false">
                    {{ t('global.cancel') }}
                </button>
                <button type="submit" class="px-5 py-2 text-white bg-indigo-600 rounded dark:bg-indigo-400" @click="handleAdd">
                    <span v-if="!isLoading">
                        <i class="mr-1 fa fa-plus"></i>
                        {{ t('loading_screen.do_add') }}
                    </span>
                    <span v-else>
                        <i class="fas fa-cog animate-spin"></i>
                        {{ t('global.loading') }}
                    </span>
                </button>
            </template>
        </modal>
    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Modal from  '../../Components/Modal';

export default {
    layout: Layout,
    components: {
        VSection,
        Modal,
    },
    props: {
        pictures: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            isAdding: false,

            failedLoad: {},
            smallSize: {},
            loaded: {},

            failedLoadCount: 0,
            smallSizeCount: 0,
            loadedCount: 0,

            image_url: '',
        };
    },
    methods: {
        async deletePicture(e, id) {
            e.preventDefault();

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.delete('/loading_screen/' + id);

                if (this.smallSize[id]) {
                    delete this.smallSize[id];

                    this.smallSizeCount = Object.values(this.smallSize).length;
                }

                if (this.failedLoad[id]) {
                    delete this.failedLoad[id];

                    this.failedLoadCount = Object.values(this.failedLoad).length;
                }

                if (this.loaded[id]) {
                    delete this.loaded[id];

                    this.loadedCount = Object.values(this.loaded).length;
                }
            } catch (e) { }

            this.isLoading = false;
        },
        imageFailed(id) {
            this.failedLoad[id] = true;

            this.failedLoadCount = Object.values(this.failedLoad).length;
        },
        imageLoaded(event, id) {
            const img = event.target;

            if (img.naturalWidth < 1920 || img.naturalHeight < 1080) {
                this.smallSize[id] = img.naturalWidth + "x" + img.naturalHeight;

                this.smallSizeCount = Object.values(this.smallSize).length;
            }

            this.loaded[id] = true;

            this.loadedCount = Object.values(this.loaded).length;
        },
        async handleAdd() {
            const url = this.image_url.trim();

            if (!url || !url.startsWith("https://")) {
                alert("Please enter a valid URL");

                return;
            }

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.post('/loading_screen', {
                    image_url: url,
                });
            } catch (e) { }

            this.isLoading = false;
            this.isAdding = false;
        },
        async refresh() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/loading_screen', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['pictures'],
                });
            } catch (e) { }

            this.isLoading = false;
        },
    },
}
</script>
