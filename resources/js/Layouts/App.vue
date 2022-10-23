<template>
    <div>

        <!-- Modals -->
        <portal-target name="modals" />

        <div class="flex flex-col h-screen">
            <!-- Navbar -->
            <navbar />

            <div class="flex flex-grow overflow-hidden">
                <!-- Sidebar -->
                <sidebar class="flex-shrink-0" v-if="!isMobile()" />

                <!-- Content -->
                <div class="flex flex-col flex-grow overflow-y-auto dark:bg-gray-800 dark:text-white" scroll-region>
                    <div class="flex-grow p-12 mobile:px-4 relative">

                        <!-- Flash message -->
                        <div>
                            <flash-message />
                        </div>

                        <!-- Header -->
                        <header class="flex flex-wrap items-start justify-between flex-grow mb-12">

                            <!-- Title -->
                            <div class="max-w-full prose dark:text-white">
                                <portal-target name="title" />
                            </div>

                            <!-- Actions -->
                            <div>
                                <portal-target name="actions" />
                            </div>

                        </header>

                        <!-- Main -->
                        <main>
                            <slot />
                        </main>

                    </div>

                    <!-- Mobile Sidebar -->
                    <sidebar class="flex-shrink-0" v-if="isMobile()" />

                    <!-- Footer -->
                    <foot />
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import FlashMessage from './../Components/FlashMessage';
import Navbar from './../Components/Navbar';
import Sidebar from './../Components/Sidebar';
import Foot from './../Components/Footer';

export default {
    components: {
        FlashMessage,
        Foot,
        Sidebar,
        Navbar,
    },
    methods: {
        isMobile() {
            return $(window).width() <= 640;
        }
    },
    updated() {
        const title = $("header h1").text().trim();

        if (title) {
            const cluster = this.$page?.auth?.cluster ? this.$page.auth.cluster.toUpperCase() : "OP-FW";

            $("title").text(cluster + " - " + title);
        }
    },
    beforeCreate() {
        this.loadLocale(this.$page.lang);
    }
};
</script>
