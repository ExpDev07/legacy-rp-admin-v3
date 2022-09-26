<template>
    <div v-if="players.length > 0" class="pt-4 mr-4">
        <h3 class="mb-2">{{ title }}</h3>
        <table class="text-sm font-mono font-medium">
            <tr v-for="(player, x) in players" :key="x">
                <td class="pr-2">
                    <a target="_blank" :href="'/players/' + player.steam" :class="color">
                        {{ usePlayerName ? player.playerName : player.name }}
                    </a>
                </td>
                <td :class="'pr-2 ' + color">
                    ({{ player.source }})
                </td>
                <td>
                    <a :class="'track-cid ' + color" href="#"
                       data-popup="true" :title="t('map.track')"
                       @click="trackServerId($event, 'server_' + player.source)">
                        {{ t('map.short.track') }}
                    </a>
                    <a :class="'highlight-cid ' + color" href="#" :title="t('map.do_highlight')"
                       @click="highlightServerId($event, player.steam)">
                        {{ t('map.short.highlight') }}
                    </a>
                </td>
            </tr>
        </table>
    </div>
</template>
<script>
export default {
    name: "SimplePlayerList",
    props: {
        trackServerId: {
            type: Function
        },
        highlightServerId: {
            type: Function
        },
        players: {
            type: Array,
            required: true
        },
        title: {
            type: String,
            required: true
        },
        color: {
            type: String,
            required: true
        },
        usePlayerName: {
            type: Boolean,
            default: false
        }
    }
}
</script>
