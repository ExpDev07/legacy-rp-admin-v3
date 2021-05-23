<template>
    <div>

        <!-- Modals -->
        <portal-target name="modals" />

        <div class="flex flex-col h-screen">
            <!-- Navbar -->
            <navbar />

            <div class="flex flex-grow overflow-hidden">
                <!-- Sidebar -->
                <sidebar class="flex-shrink-0" />

                <!-- Content -->
                <div class="flex flex-col flex-grow overflow-y-auto" scroll-region>
                    <div class="flex-grow p-12">

                        <!-- Flash message -->
                        <div>
                            <flash-message />
                        </div>

                        <!-- Header -->
                        <header class="flex flex-grow items-start justify-between mb-12">

                            <!-- Title -->
                            <div class="prose max-w-full">
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
};
</script>
