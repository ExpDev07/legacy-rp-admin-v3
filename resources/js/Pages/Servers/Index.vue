<template>
    <div>

        <portal to="title">
            <h1>
                Servers
            </h1>
            <p>
                An overview of the game servers.
            </p>
        </portal>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';

export default {
    layout: Layout,
    components: {
        VSection,
    },
    props: {
        servers: {
            type: Object,
            required: true,
        },
    },
}
</script>
