<template>
    <div>

        <div class="flex flex-col h-screen">
            <smallNavbar />

            <div class="flex flex-grow overflow-hidden">
                <!-- Content -->
                <div class="flex flex-col flex-grow overflow-y-auto dark:bg-gray-800 dark:text-white" scroll-region>
                    <div class="flex-grow px-10 py-8 pt-5 mobile:px-4">

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
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import FlashMessage from './../Components/FlashMessage';
import SmallNavbar from './../Components/SmallNavbar';

export default {
    components: {
        FlashMessage,
        SmallNavbar,
    },
    beforeCreate() {
        this.loadLocale(this.$page.lang);
    }
};
</script>
