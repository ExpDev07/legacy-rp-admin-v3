<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white !mb-2">
                <span id="map_title">{{ t('map.title') }}</span>
                <select class="inline-block w-90 ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                        id="server">
                    <option v-for="server in servers" :key="server.name" :value="server.name">{{ server.name }}</option>
                </select>
            </h1>
            <p>
                <span v-html="data" class="block">{{ data }}</span>
                <span class="block text-xxs text-muted dark:text-dark-muted mt-0 leading-3" v-if="lastConnectionError">{{
                        lastConnectionError
                    }}</span>
            </p>
        </portal>

        <portal to="actions">
            <div class="mb-2">
                <!-- Toggle On-Duty List -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isScreenshot = true">
                    <i class="fas fa-camera"></i>
                    {{ t('map.screenshot') }}
                </button>

                <!-- Toggle On-Duty List -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isShowingOnDutyList = !isShowingOnDutyList">
                    <i class="fas fa-gavel"></i>
                    {{ t('map.toggle_duty_list') }}
                </button>

                <!-- Play/Pause -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isPaused = true" v-if="!isPaused">
                    <i class="fas fa-pause"></i>
                    {{ t('map.pause') }}
                </button>
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isPaused = false" v-if="isPaused">
                    <i class="fas fa-play"></i>
                    {{ t('map.play') }}
                </button>
            </div>
        </portal>

        <!-- Area Add -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k" v-if="isAddingDetectionArea">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.area_title') }}
                </h3>

                <!-- Radius -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 pt-2 font-bold" for="area_radius">
                        {{ t('map.area_radius') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" min="1" max="5000"
                           id="area_radius" value="5" v-model="form.area_radius"/>
                </div>

                <!-- Type -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 pt-2 font-bold">
                        {{ t('map.area_type.title') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="area_type"
                            v-model="form.area_type">
                        <option value="normal">{{ t('map.area_type.normal') }}</option>
                        <option value="persistent">{{ t('map.area_type.persistent') }}</option>
                    </select>
                </div>

                <hr>

                <h4 class="my-2">
                    {{ t('map.area_filter') }}
                    <sup>
                        <a href="#" class="text-success dark:text-dark-success font-bold text-lg"
                           @click="addFilter($event)">+</a>
                    </sup>
                </h4>

                <!-- Filters -->
                <div class="w-full flex justify-between mb-2" v-if="form.filters.length === 0">
                    {{ t('map.filter_none') }}
                </div>
                <div class="w-full flex justify-between mb-2" v-for="(filter, index) in form.filters" :key="index"
                     v-else>
                    <label class="mr-4 block w-1/4 pt-2 font-bold">
                        {{ t('map.area_filters.title') }} #{{ index }}
                        <sup>
                            <a href="#" class="text-red-500 font-bold"
                               @click="removeFilter($event, index)">&#x1F5D9;</a>
                        </sup>
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                            v-model="form.filters[index]">
                        <option value="is_vehicle">{{ t('map.area_filters.is_vehicle') }}</option>
                        <option value="is_not_vehicle">{{ t('map.area_filters.is_not_vehicle') }}</option>
                        <option value="is_dead">{{ t('map.area_filters.is_dead') }}</option>
                        <option value="is_not_dead">{{ t('map.area_filters.is_not_dead') }}</option>
                        <option value="is_staff">{{ t('map.area_filters.is_staff') }}</option>
                        <option value="is_not_staff">{{ t('map.area_filters.is_not_staff') }}</option>
                        <option value="is_invisible">{{ t('map.area_filters.is_invisible') }}</option>
                        <option value="is_not_invisible">{{ t('map.area_filters.is_not_invisible') }}</option>
                        <option value="is_highlighted">{{ t('map.area_filters.is_highlighted') }}</option>
                        <option value="is_not_highlighted">{{ t('map.area_filters.is_not_highlighted') }}</option>
                        <option value="is_male">{{ t('map.area_filters.is_male') }}</option>
                        <option value="is_female">{{ t('map.area_filters.is_female') }}</option>
                    </select>
                </div>

                <hr>

                <p class="my-2">
                    <span class="font-bold">{{ t('map.area_type.normal') }}</span>:
                    {{ t('map.area_type.normal_description') }}<br>
                    <span class="font-bold">{{ t('map.area_type.persistent') }}</span>:
                    {{ t('map.area_type.persistent_description') }}
                </p>

                <!-- Buttons -->
                <div class="flex items-center mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="confirmArea">
                        <i class="mr-1 fas fa-plus"></i>
                        {{ t('map.area_add') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isAddingDetectionArea = false">
                        {{ t('global.cancel') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Notify Add -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k" v-if="isNotification">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.notify_add') }}
                </h3>

                <!-- Steam Identifier -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 pt-2 font-bold" for="notify_steam">
                        {{ t('map.notify_steam') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="notify_steam"
                           v-model="form.notify_steam"/>
                </div>

                <!-- Type -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 pt-2 font-bold">
                        {{ t('map.notify_type') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="notify_type"
                            v-model="form.notify_type">
                        <option value="invisible">{{ t('map.notify_invisible') }}</option>
                        <option value="load">{{ t('map.notify_load') }}</option>
                        <option value="unload">{{ t('map.notify_unload') }}</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="confirmNotification">
                        <i class="mr-1 fas fa-plus"></i>
                        {{ t('global.confirm') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isNotification = false">
                        {{ t('global.cancel') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Screenshot -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k" v-if="isScreenshot">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.screenshot') }}
                </h3>

                <p v-if="screenshotError" class="text-danger dark:text-dark-danger font-semibold mb-3">
                    {{ screenshotError }}
                </p>

                <a v-if="screenshotImage" class="w-full" :href="screenshotImage" target="_blank">
                    <img :src="screenshotImage" alt="Screenshot" class="w-full" />
                </a>
                <p v-if="screenshotImage" class="mt-3 text-sm">
                    {{ t('map.screenshot_description') }}
                </p>

                <!-- Steam Identifier -->
                <div class="w-full p-3 flex justify-between px-0" v-else>
                    <label class="mr-4 block w-1/4 pt-2 font-bold" for="screenshot_id">
                        {{ t('map.screenshot_id') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="screenshot_id"
                           v-model="form.screenshotId"/>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="createScreenshot" v-if="!screenshotImage">
                        <span v-if="!isScreenshotLoading">
                            <i class="fas fa-camera mr-1"></i>
                            {{ t('map.screenshot_create') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-cog animate-spin mr-1"></i>
                            {{ t('global.loading') }}
                        </span>
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isScreenshot = false; screenshotImage = null; screenshotError = null">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <template>
            <div class="-mt-12" id="map-wrapper">
                <div class="flex flex-wrap justify-between mb-2 w-map max-w-full">
                    <div class="flex flex-wrap">
                        <input type="text"
                               class="form-control w-56 rounded border block mobile:w-full px-4 py-2 bg-gray-200 dark:bg-gray-600"
                               :placeholder="t('map.track_placeholder')" v-model="tracking.id"/>
                        <select
                            class="block w-44 ml-2 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded mobile:w-full mobile:m-0 mobile:mt-1"
                            v-model="tracking.type">
                            <option value="server_">{{ t('map.track_server') }}</option>
                            <option value="">{{ t('map.track_steam') }}</option>
                            <option value="player_">{{ t('map.track_character') }}</option>
                        </select>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="trackId(tracking.type + tracking.id)">
                            {{ t('map.do_track') }}
                        </button>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                            @click="stopTracking()" v-if="container.isTrackedPlayerVisible">
                            {{ t('global.stop') }}
                        </button>
                    </div>
                    <div class="flex flex-wrap">
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="isNotification = true">
                            {{ t('map.notify_add') }}
                        </button>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="addArea">
                            {{ t('map.area_add') }}
                        </button>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="addArea(true)"
                            :title="t('map.quick_area_title')"
                        >
                            {{ t('map.quick_area') }}
                        </button>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="advancedTracking = !advancedTracking"
                            :title="advancedTracking ? t('global.enabled') : t('global.disabled')"
                        >
                            {{ t('map.advanced_track') }}
                            <i class="fas fa-check ml-1" v-if="advancedTracking"></i>
                            <i class="fas fa-times ml-1" v-else></i>
                        </button>
                    </div>
                </div>
                <div class="relative w-map max-w-full">
                    <div id="map" class="w-map max-w-full relative h-max"></div>
                    <pre class="bg-opacity-70 bg-white coordinate-attr absolute bottom-0 left-0 cursor-pointer z-1k"
                         v-if="clickedCoords"><span @click="copyText($event, clickedCoords)">{{ clickedCoords }}</span> / <span
                        @click="copyText($event, coordsCommand)">{{ t('map.command') }}</span></pre>
                    <pre
                        class="w-map-gauge leaflet-attr bg-opacity-70 bg-white absolute bottom-attr2 right-0 z-1k p-2 text-gray-800 text-xs"
                        v-if="advancedTracking && container.isTrackedPlayerVisible"
                    >{{ tracking.data.advanced }}</pre>
                    <div
                        class="w-map-gauge leaflet-attr bg-opacity-70 bg-white absolute bottom-attr right-0 z-1k px-2 pt-2 pb-1 flex"
                        :class="{'hidden' : !advancedTracking || !container.isTrackedPlayerVisible}"
                    >
                        <div class="relative w-map-other-gauge">
                            <img src="/images/height-indicator.png" style="height: 90px" alt="Height indicator"/>
                            <div
                                class="font-bold absolute border-b-2 border-gray-700 left-8 text-gray-700 w-map-height-ind text-right text-xxs leading-3"
                                :style="'bottom: ' + tracking.data.alt + '%;'"
                            >
                                {{ tracking.data.altitude }}
                            </div>
                        </div>
                        <vue-speedometer
                            class="inline-block"
                            :value="tracking.data.speed"
                            labelFontSize="12px"
                            :ringWidth="20"
                            :height="90"
                            :width="120"
                            startColor="#90EF90"
                            endColor="#fa1e43"
                            :minValue="0"
                            :maxValue="360"
                            :segments="4"
                            currentValueText="${value}mph"
                            valueTextFontSize="14px"
                            :needleHeightRatio="0.7"
                        />
                    </div>

                    <div v-if="rightClickedPlayer.id"
                         class="absolute z-1k top-0 left-0 right-0 bottom-0 bg-black bg-opacity-70">
                        <div
                            class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded">
                            <h2 class="text-xl mb-2" v-html="rightClickedPlayer.name">{{ rightClickedPlayer.name }}</h2>
                            <p class="text-muted dark:text-dark-muted mb-1">
                                <span class="font-semibold">{{ t('players.steam') }}:</span>
                                <a :href="'/players/' + rightClickedPlayer.id" target="_blank"
                                   class="text-blue-600 dark:text-blue-400 italic">{{ rightClickedPlayer.id }}</a>
                            </p>
                            <p class="text-muted dark:text-dark-muted mb-3">
                                <span class="font-semibold">{{ t('players.name') }}:</span>
                                <span class="italic">{{ rightClickedPlayer.playerName }}</span>
                            </p>
                            <div class="flex justify-between">
                                <button
                                    class="px-5 py-2 mr-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                    @click="trackId(rightClickedPlayer.id)"
                                    v-if="!rightClickedPlayer.tracked">
                                    {{ t('map.do_track') }}
                                </button>
                                <button
                                    class="px-5 py-2 mr-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger"
                                    @click="stopTracking()"
                                    v-else>
                                    {{ t('map.stop_track') }}
                                </button>

                                <button
                                    class="px-5 py-2 mr-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                    @click="highlightSteam(rightClickedPlayer.id)"
                                    v-if="!(rightClickedPlayer.id in highlightedPeople)">
                                    {{ t('map.do_highlight') }}
                                </button>
                                <button
                                    class="px-5 py-2 mr-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger"
                                    @click="stopHighlight($event, rightClickedPlayer.id)"
                                    v-else>
                                    {{ t('map.stop_highlight') }}
                                </button>

                                <button type="button"
                                        class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                        @click="rightClickedPlayer.id = null">
                                    {{ t('global.close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Legend -->
                <div class="my-2 flex flex-wrap -mx-2 justify-between text-xs w-map max-w-full">
                    <div class="mx-2">
                        <img src="/images/icons/circle.png" class="w-map-icon inline-block" alt="on foot"/>
                        <span class="leading-map-icon">Someone is on foot</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_green.png" class="w-map-icon inline-block" alt="invisible"/>
                        <span class="leading-map-icon">Someone is invisible</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_red.png" class="w-map-icon inline-block" alt="passenger"/>
                        <span class="leading-map-icon">Someone is a passenger</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/skull.png" class="w-map-icon inline-block" alt="dead"/>
                        <span class="leading-map-icon">Someone is dead</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/skull_red.png" class="w-map-icon inline-block" alt="dead passenger"/>
                        <span class="leading-map-icon">Someone is dead and a passenger</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_police.png" class="w-map-icon inline-block" alt="police"/>
                        <span class="leading-map-icon">Someone is on duty as police</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_ems.png" class="w-map-icon inline-block" alt="ems"/>
                        <span class="leading-map-icon">Someone is on duty as ems</span>
                    </div>
                </div>

                <!-- Detection Areas -->
                <div class="flex flex-wrap" v-if="detectionAreas.length > 0">
                    <div class="pt-4 mr-4" v-for="(area, index) in detectionAreas" :key="index">
                        <h3 class="mb-2">
                            {{ t('map.area_label', index + 1) }}
                            <sup>
                                ({{ Object.keys(area.players).length }})
                                <a href="#" class="text-red-500 font-bold" @click="removeArea($event, index)"
                                   :title="t('global.remove')">&#x1F5D9;</a>
                            </sup>
                        </h3>
                        <table class="text-xs font-mono font-medium">
                            <tr v-if="Object.keys(area.players).length === 0">
                                {{ t('map.area_none') }}
                            </tr>
                            <tr v-for="(player, steam) in area.players" :key="steam" v-else>
                                <td class="pr-2">
                                    <a class="text-yellow-500" target="_blank"
                                       :href="'/players/' + player.steam">{{ player.name }}</a>
                                </td>
                                <td class="pr-2 text-yellow-500">
                                    ({{ player.source }})
                                </td>
                                <td class="pr-2" v-if="player.inside">
                                    {{ t('map.area_inside') }}
                                </td>
                                <td class="pr-2" v-else>
                                    {{ t('map.area_not_inside') }}
                                </td>
                                <td>
                                    <span class="text-yellow-600" :title="t('map.invisible_time', formatSeconds(Math.round(player.invisible_time / 1000)))" v-if="player.invisible_time > 0">
                                        [I]
                                    </span>
                                    <a class="track-cid text-yellow-600" href="#" :data-trackid="'server_' + player.source" data-popup="true">
                                        {{ t('map.short.track') }}
                                    </a>
                                    <a class="highlight-cid text-yellow-600" href="#" :data-steam="player.steam">
                                        {{ t('map.short.highlight') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <!-- Invisible Players -->
                    <div v-if="invisiblePeople.length > 0" class="pt-4 mr-4 font-medium">
                        <h3 class="mb-2">{{ t('map.invisible_title') }}</h3>
                        <table class="text-sm font-mono">
                            <tr v-for="(player, x) in invisiblePeople" :key="x">
                                <td class="pr-2">
                                    <a class="dark:text-red-400 text-red-600" target="_blank"
                                       :href="'/players/' + player.steam">{{ player.name }}</a>
                                </td>
                                <td class="pr-2 dark:text-red-400 text-red-600">
                                    ({{ player.source }})
                                </td>
                                <td class="pr-2">
                                    {{ t('map.invisible') }}
                                </td>
                                <td>
                                    <a class="track-cid dark:text-red-400 text-red-600" href="#"
                                       :data-trackid="'server_' + player.source" data-popup="true">
                                        {{ t('map.short.track') }}
                                    </a>
                                    <a class="highlight-cid dark:text-red-400 text-red-600" href="#"
                                       :data-steam="player.steam">{{ t('map.short.highlight') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Highlighted Players -->
                    <div v-if="Object.keys(highlightedPeople).length > 0" class="pt-4 mr-4">
                        <h3 class="mb-2">{{ t('map.highlighted_title') }}</h3>
                        <table class="text-sm font-mono text-map-highlight font-medium">
                            <tr v-for="(player, steam) in highlightedPeople" :key="steam" v-if="player !== true">
                                <td class="pr-2">
                                    <a target="_blank" :href="'/players/' + steam">{{ player.name }}</a>
                                </td>
                                <td class="pr-2">
                                    ({{ player.source }})
                                </td>
                                <td class="pr-2">
                                    {{ t('map.highlighted') }}
                                </td>
                                <td>
                                    <a class="track-cid" href="#" :data-trackid="'server_' + player.source" data-popup="true">
                                        {{ t('map.short.track') }}
                                    </a>
                                    <a href="#" @click="stopHighlight($event, steam)">{{ t('map.short.remove') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- AFK Players -->
                    <div v-if="afkPeople.length > 0" class="pt-4 mr-4 font-medium">
                        <h3 class="mb-2">{{ t('map.afk_title') }}</h3>
                        <table class="text-sm font-mono">
                            <tr v-for="(player, x) in afkPeople" :key="x"
                                :title="player.is_staff ? t('map.is_staff') : ''">
                                <td class="pr-2">
                                    <a :style="'color:' + player.color" target="_blank"
                                       :href="'/players/' + player.steam">{{ player.name }}</a>
                                </td>
                                <td class="pr-2" :style="'color:' + player.color">
                                    ({{ player.source }})
                                </td>
                                <td class="pr-2" :title="player.afk_title">
                                    {{ t('map.afk_move', formatSeconds(player.afk)) }}
                                </td>
                                <td>
                                    <a class="track-cid" :style="'color:' + player.color" href="#"
                                       :data-trackid="'server_' + player.source" data-popup="true">{{
                                            t('map.short.track')
                                        }}</a>
                                    <a class="highlight-cid" :style="'color:' + player.color" href="#"
                                       :data-steam="player.steam">{{ t('map.short.highlight') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Notifications -->
                    <div v-if="!container.notifier.isEmpty()" class="pt-4">
                        <h3 class="mb-2">{{ t('map.notify') }}</h3>
                        <table class="text-sm font-mono font-medium">
                            <tr v-for="(player, steam) in container.notifier.notifications.invisible"
                                :key="'invisible_' + steam">
                                <td class="pr-2">
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-if="player === true">{{ steam }}</a>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else>{{ player.name }}</a>
                                </td>
                                <td class="pr-2">
                                    {{ t('map.notify_invisible') }}
                                </td>
                                <td>
                                    <a class="dark:text-red-400 text-red-600" href="#"
                                       @click="stopNotify($event, steam, 'invisible')">{{ t('map.short.remove') }}</a>
                                </td>
                            </tr>
                            <tr v-for="(player, steam) in container.notifier.notifications.load" :key="'load_' + steam">
                                <td class="pr-2">
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-if="player === true">{{ steam }}</a>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else>{{ player.name }}</a>
                                </td>
                                <td class="pr-2">
                                    {{ t('map.notify_load') }}
                                </td>
                                <td>
                                    <a class="dark:text-red-400 text-red-600" href="#"
                                       @click="stopNotify($event, steam, 'load')">{{ t('map.short.remove') }}</a>
                                </td>
                            </tr>
                            <tr v-for="(player, steam) in container.notifier.notifications.unload"
                                :key="'unload_' + steam">
                                <td class="pr-2">
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-if="player === true">{{ steam }}</a>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else>{{ player.name }}</a>
                                </td>
                                <td class="pr-2">
                                    {{ t('map.notify_unload') }}
                                </td>
                                <td>
                                    <a class="dark:text-red-400 text-red-600" href="#"
                                       @click="stopNotify($event, steam, 'unload')">{{ t('map.short.remove') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <simple-player-list
                        v-if="isShowingOnDutyList"
                        :title="t('map.duty_list_pd')"
                        :players="container.on_duty.pd"
                        color="text-map-police"
                    ></simple-player-list>

                    <simple-player-list
                        v-if="isShowingOnDutyList"
                        :title="t('map.duty_list_ems')"
                        :players="container.on_duty.ems"
                        color="text-map-ems"
                    ></simple-player-list>

                    <simple-player-list
                        :title="t('map.staff_online')"
                        :players="container.staff"
                        color="text-map-staff"
                        :usePlayerName="true"
                    ></simple-player-list>
                </div>
            </div>
        </template>

        <div v-if="loadingScreenStatus" class="fixed top-0 left-0 right-0 bottom-0 z-2k bg-black bg-opacity-75">
            <div class="text-2xl text-white absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2">
                <i class="fas fa-cog animate-spin mr-1"></i>
                {{ loadingScreenStatus }}
            </div>
        </div>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import SimplePlayerList from './../../Components/Map/SimplePlayerList';
import L from "leaflet";
import {GestureHandling} from "leaflet-gesture-handling";
import "leaflet-rotatedmarker";
import 'leaflet-fullscreen';
import 'leaflet.heat';
import VueSpeedometer from "vue-speedometer";

import PlayerContainer from './PlayerContainer';
import Player from './Player';
import Vector3 from "./Vector3";
import Bounds from './map.config';
import DataCompressor from "./DataCompressor";
import DetectionArea from "./DetectionArea";

(function (global) {
    let MarkerMixin = {
        _updateZIndex: function (offset) {
            this._icon.style.zIndex = this.options.forceZIndex ? (this.options.forceZIndex + (this.options.zIndexOffset || 0)) : (this._zIndex + offset);
        },
        setForceZIndex: function (forceZIndex) {
            this.options.forceZIndex = forceZIndex ? forceZIndex : null;
        }
    };
    if (global) global.include(MarkerMixin);
})(L.Marker);

window.instance = null;

export default {
    layout: Layout,
    components: {
        VSection,
        VueSpeedometer,
        SimplePlayerList,
    },
    props: {
        servers: {
            type: Array,
            required: true
        },
        staff: {
            type: Array,
            required: true
        },
        blips: {
            type: Array,
            required: true
        },
        token: {
            type: String,
            required: true
        },
        myself: {
            type: String,
            required: true
        },
        cluster: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            map: null,
            container: new PlayerContainer(this.staff),
            markers: {},
            data: this.t('map.loading'),
            connection: null,
            isPaused: false,
            isNotification: false,
            isShowingOnDutyList: false,
            firstRefresh: true,
            clickedCoords: '',
            rawClickedCoords: null,
            coordsCommand: '',
            afkPeople: [],
            invisiblePeople: [],
            notifyLoad: {},
            notifyUnload: {},
            openPopup: null,
            isDragging: false,
            isAddingDetectionArea: false,
            whereAmI: null,
            rightClickedPlayer: {
                id: null,
                name: null,
                playerName: null,
                tracked: false
            },
            form: {
                area_radius: 0,
                area_type: 'normal',
                area_location: {
                    x: 0,
                    y: 0
                },
                filters: [],

                notify_steam: '',
                notify_type: 'load',

                screenshotId: 0
            },
            layers: {
                "Players": L.layerGroup(),
                "Emergency Vehicles": L.layerGroup(),
                "Vehicles": L.layerGroup(),
                "Blips": L.layerGroup(),
            },
            detectionAreas: [],
            tracking: {
                id: '',
                type: 'server_',
                data: {
                    speed: 0,
                    alt: 0,
                    altitude: '0m',
                    advanced: 'Loading...'
                }
            },
            lastConnectionError: null,
            lastSocketMessage: null,
            socketStart: 0,
            characters: {},
            highlightedPeople: {},
            advancedTracking: false,
            cayoCalibrationMode: false, // Set this to true to recalibrate the cayo perico map

            isScreenshot: false,
            isScreenshotLoading: false,
            screenshotImage: null,
            screenshotError: null,

            heatmapLayer: null,
            loadingScreenStatus: null
        };
    },
    methods: {
        copyText(e, text) {
            if (e !== null) {
                e.preventDefault();
            }

            this.copyToClipboard(text);
        },
        formatSeconds(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        stopTracking() {
            window.location.hash = '';

            this.rightClickedPlayer.id = null;
        },
        trackId(id) {
            window.location.hash = id;
            this.firstRefresh = true;

            this.rightClickedPlayer.id = null;
        },
        addFilter(e) {
            e.preventDefault();

            this.form.filters.push('is_invisible');
        },
        removeFilter(e, index) {
            e.preventDefault();

            this.form.filters.splice(index, 1);
        },
        highlightSteam(steam) {
            this.highlightedPeople[steam] = true;

            this.rightClickedPlayer.id = null;
        },
        stopHighlight(e, steam) {
            e.preventDefault();

            delete this.highlightedPeople[steam];

            this.rightClickedPlayer.id = null;
        },
        stopNotify(e, steam, type) {
            e.preventDefault();

            if (type === 'load') {
                this.container.notifier.removeNotify('load', steam);
            } else if (type === 'unload') {
                this.container.notifier.removeNotify('unload', steam);
            }
        },
        confirmNotification() {
            this.container.notifier.on(this.form.notify_type, this.form.notify_steam);

            this.form.notify_steam = '';
            this.form.notify_type = 'load';

            this.isNotification = false;
        },
        confirmArea() {
            if (this.form.area_radius < 1 || this.form.area_radius > 5000) {
                return alert(this.t('map.area_inv_radius'));
            }

            this.isAddingDetectionArea = false;

            const area = new DetectionArea(
                this.detectionAreas.length + 1,
                Vector3.fromGameCoords(parseInt(this.form.area_location.x), parseInt(this.form.area_location.y), 0),
                parseInt(this.form.area_radius),
                [...new Set(this.form.filters)],
                this.form.area_type === 'persistent'
            );

            const marker = area.getMarker(this);
            marker.addTo(this.map);

            const circle = area.getCircle();
            circle.addTo(this.map);

            this.detectionAreas.push(area);

            this.form.area_location = null;
            this.form.area_type = 'normal';
            this.form.area_radius = 5;
            this.form.filters = [];
        },
        addArea(quick) {
            if (quick) {
                if (!this.whereAmI) {
                    return alert(this.t('map.area_no_whereami'));
                }

                this.form.area_location = this.whereAmI;
                this.form.area_radius = 50;
                this.form.area_type = 'persistent';

                this.confirmArea();
                return;
            }

            if (!this.rawClickedCoords) {
                return alert(this.t('map.area_no_location'));
            }

            this.form.area_location = this.rawClickedCoords;
            this.form.area_radius = 5;

            this.isAddingDetectionArea = true;
        },
        removeArea(e, index) {
            e.preventDefault();

            this.map.removeLayer(this.detectionAreas[index].marker);
            this.map.removeLayer(this.detectionAreas[index].circle);

            let areas = [];
            for (let x = 0; x < this.detectionAreas.length; x++) {
                if (x !== index) {
                    const area = this.detectionAreas[x];
                    area.id = areas.length + 1;

                    areas.push(area);
                }
            }

            this.detectionAreas = areas;
        },
        humanizeMilliseconds(ms) {
            const sec = Math.round(ms / 1000);

            return this.$options.filters.humanizeSeconds(sec) + ' (' + sec + 's)';
        },
        hostname(isSocket) {
            const isDev = window.location.hostname === 'localhost';

            if (isSocket) {
                return isDev ? 'ws://localhost:9999' : 'wss://map.legacy-roleplay.com';
            } else {
                return isDev ? 'http://localhost:9999' : 'https://map.legacy-roleplay.com';
            }
        },
        async createScreenshot() {
            if (this.isScreenshotLoading) {
                return;
            }
            this.isScreenshotLoading = true;
            this.screenshotError = null;

            try {
                const result = await axios.post('/api/screenshot/' + $('#server').val() + '/' + this.form.screenshotId);
                this.isScreenshotLoading = false;

                if (result.data) {
                    if (result.data.status) {
                        console.info('Screenshot of ID ' + this.form.screenshotId, result.data.data.url);

                        this.screenshotImage = result.data.data.url;
                    } else {
                        this.screenshotError = result.data.message ? result.data.message : this.t('map.screenshot_failed');
                    }
                }
            } catch(e) {
                this.screenshotError = this.t('map.screenshot_failed');
            }
        },
        async renderHeatMap(date) {
            if (this.loadingScreenStatus) {
                return;
            }
            this.loadingScreenStatus = this.t('map.heatmap_fetch');

            const server = $('#server').val(),
                heatmap = await this.loadHeatMap(server, date);

            this.loadingScreenStatus = this.t('map.heatmap_render');

            if (this.heatmapLayer) {
                this.map.removeLayer(this.heatmapLayer);
                this.heatmapLayer = null;
            }

            if (heatmap) {
                $('.leaflet-control-layers-selector').each(function() {
                    if ($(this).prop('checked')) {
                        $(this).trigger('click');
                    }
                });

                this.heatmapLayer = L.heatLayer(heatmap, {
                    radius: 10,
                    minOpacity: 0.65,
                    maxZoom: 7
                });

                this.heatmapLayer.addTo(this.map);
            }

            this.loadingScreenStatus = null;
        },
        async loadHeatMap(server, date) {
            this.loadingScreenStatus = this.t('map.heatmap_fetch');
            try {
                const result = await axios.get(this.hostname(false) + '/history/heatmap/' + server + '/' + date + '?token=' + this.token + '&cluster=' + this.cluster);

                this.loadingScreenStatus = this.t('map.heatmap_parse');
                if (result.data && result.data.status) {
                    const max = Math.max(...Object.values(result.data.data));

                    let heatmap = [];
                    for (const coords in result.data.data) {
                        if (Object.hasOwnProperty(coords)) continue;
                        const tmp = coords.split('/'),
                            location = Vector3.fromGameCoords(parseInt(tmp[0]), parseInt(tmp[1]), 0).toMap();

                        heatmap.push([
                            location.lat,
                            location.lng,
                            result.data.data[coords] / max
                        ]);
                    }

                    return heatmap;
                } else if (result.data && !result.data.status) {
                    console.error(result.data.error);
                }
            } catch(e) {
                console.error(e);
            }

            return null;
        },
        getOTToken() {
            const _this = this;

            return new Promise(function (resolve, reject) {
                $.get(_this.hostname(false) + '/token?token=' + _this.token + '&cluster=' + _this.cluster, function (data) {
                    if (data.status) {
                        resolve(data.token);
                    } else {
                        reject(data.error);
                    }
                }).fail(reject);
            });
        },
        async doMapRefresh(server) {
            const _this = this;

            if (this.connection) {
                this.connection.close();
            }

            try {
                const token = await this.getOTToken();

                this.lastSocketMessage = null;
                this.lastConnectionError = null;
                this.connection = new WebSocket(this.hostname(true) + "/socket?ott=" + token + "&server=" + encodeURIComponent(server) + "&cluster=" + this.cluster);
                this.socketStart = Date.now();

                this.connection.onmessage = async function (event) {
                    _this.lastSocketMessage = event.data;

                    try {
                        const unzipped = await DataCompressor.GUnZIP(event.data),
                            data = JSON.parse(unzipped);

                        await _this.renderMapData(data);

                        _this.firstRefresh = false;
                    } catch (e) {
                        console.error('Failed to parse socket message ', e)
                    }
                }

                this.connection.onclose = async function () {
                    let connectionTime = _this.$moment.duration(Date.now() - _this.socketStart, 'milliseconds').format('h[h] m[m] s[s]');

                    if (_this.lastSocketMessage) {
                        try {
                            const unzipped = await DataCompressor.GUnZIP(_this.lastSocketMessage),
                                data = JSON.parse(unzipped);

                            if ('status' in data && 'message' in data) {
                                _this.lastConnectionError = data.status + ' - ' + data.message;
                                console.info('WebSocket:', _this.lastConnectionError);
                                console.info('WebSocket (RAW):', data);
                            }
                        } catch (e) {
                        }
                    }

                    if (_this.lastConnectionError) {
                        _this.data = _this.t('map.closed_expected', server, connectionTime);
                    } else {
                        _this.data = _this.t('map.closed_unexpected', server, connectionTime);
                    }

                    // Try reconnecting if the socket was active for more than 30 seconds
                    if (Date.now() - _this.socketStart > 30 * 1000) {
                        _this.data += ' ' + _this.t('map.try_reconnect');

                        setTimeout(function () {
                            _this.doMapRefresh(server);
                        }, 3000);
                    }
                };
            } catch (e) {
                this.data = this.t('map.closed_unexpected', server, '1 second');

                console.error('Failed to connect to socket', e);
            }
        },
        addToLayer(marker, layer) {
            const _this = this;

            $.each(this.layers, function (key) {
                if (layer !== key) {
                    _this.layers[key].removeLayer(marker);
                }
            });

            this.layers[layer].addLayer(marker);
        },
        getLayer(player) {
            const vehicle = player.vehicle;

            if (vehicle && (vehicle.icon.type === 'police_car' || vehicle.icon.type === 'ems_car')) {
                return "Emergency Vehicles";
            }
            if (vehicle) {
                return "Vehicles";
            } else {
                return "Players";
            }
        },
        async renderMapData(data) {
            if (this.isPaused || this.isDragging) {
                return;
            }

            if (this.container.isTrackedPlayerVisible) {
                this.map.dragging.disable();
            } else {
                this.map.dragging.enable();
            }

            data = DataCompressor.decompressData(data);

            if (data && 'status' in data && data.status) {
                this.data = this.t('map.advanced_error', $('#server option:selected').text(), data.message);
            } else if (DataCompressor.isValid(data)) {
                if (this.map) {
                    const _this = this;

                    this.container.updatePlayers(data, this);

                    let unknownCharacters = [],
                        foundTracked = false;

                    this.container.eachPlayer(function (id, player) {
                        if (!player.character) {
                            return;
                        }

                        if (player.player.steam === _this.myself) {
                            _this.whereAmI = player.location.toGame();
                        }

                        const characterID = player.getCharacterID();

                        if (characterID && !unknownCharacters.includes(characterID) && !(characterID in _this.characters)) {
                            unknownCharacters.push(characterID);
                        }

                        if (player.isTracked()) {
                            _this.map.setView(player.location.toMap(), _this.firstRefresh ? 7 : _this.map.getZoom(), {
                                duration: 0.1
                            });

                            _this.tracking.data.speed = player.speed;

                            const feet = Math.round(player.location.z * 3.281);
                            _this.tracking.data.alt = (feet / 5000) * 100;
                            _this.tracking.data.alt = _this.tracking.data.alt > 99 ? 99 : _this.tracking.data.alt;
                            _this.tracking.data.altitude = feet + 'ft';

                            let trackingInfo = [
                                player.getTitle(),
                                'Coords: ' + player.location.toStringGame()
                            ];
                            !player.vehicle || trackingInfo.push('Vehicle: ' + player.vehicle.model);
                            player.afk < 15 || trackingInfo.push('AFK since ' + _this.$options.filters.humanizeSeconds(player.afk.time));
                            _this.tracking.data.advanced = trackingInfo.join("\n");

                            if (_this.firstRefresh) {
                                _this.openPopup = id;
                            }

                            if (!_this.isScreenshot) {
                                _this.form.screenshotId = player.player.source;
                            }

                            foundTracked = true;
                        }

                        if (player.player.steam in _this.highlightedPeople) {
                            _this.highlightedPeople[player.player.steam] = {
                                name: player.character.name,
                                source: player.player.source,
                                cid: player.character.id
                            };
                        }

                        if (!(id in _this.markers)) {
                            _this.markers[id] = Player.newMarker();
                        }
                        _this.markers[id] = player.updateMarker(_this.markers[id], _this.highlightedPeople, _this.container.vehicles);

                        _this.addToLayer(_this.markers[id], _this.getLayer(player));

                        if (_this.markers[id]._icon) {
                            _this.markers[id]._icon.dataset.playerId = id;
                        }

                        if (_this.openPopup === id) {
                            _this.markers[id].openPopup();
                            _this.openPopup = null;
                        }
                    });

                    for (let x=0;x<this.detectionAreas.length;x++) {
                        this.detectionAreas[x].checkPlayers(Object.values(this.container.players), this.characters, this.highlightedPeople);
                    }

                    for (const id in this.markers) {
                        if (!this.markers.hasOwnProperty(id) || this.container.isActive(id)) {
                            continue;
                        }

                        this.map.removeLayer(this.markers[id]);
                        delete this.markers[id];

                        this.container.remove(id);
                    }

                    this.afkPeople = this.container.afk;
                    this.invisiblePeople = this.container.invisible;

                    this.data = this.t(
                        'map.data',
                        Object.keys(this.markers).length
                    ) + '<span class="block text-xs leading-3">' + this.t(
                        'map.data_stats',
                        this.container.stats.police,
                        this.container.stats.ems,
                        this.container.stats.staff,
                        this.container.stats.unloaded
                    ) + '</span>';

                    if (!foundTracked) {
                        window.location.hash = '';
                    }

                    if (unknownCharacters.length > 0) {
                        // Prevent it being requested twice while the other is still loading
                        $.each(unknownCharacters, function (_, id) {
                            _this.characters[id] = null;
                        });

                        axios.post('/api/characters', {
                            ids: unknownCharacters
                        }).then(function (result) {
                            if (result.data && result.data.status) {
                                $.each(result.data.data, function (_, ch) {
                                    _this.characters[ch.character_id] = ch;
                                });
                            }
                        });
                    }
                }
            } else {
                this.data = this.t('map.error', $('#server option:selected').text());
            }
        },
        async buildMap() {
            if (this.map) {
                return;
            }
            const _this = this;

            L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

            this.map = L.map('map', {
                crs: L.CRS.Simple,
                gestureHandling: true,
                minZoom: 1,
                maxZoom: 8,
                maxBounds: L.latLngBounds(L.latLng(0, 0), L.latLng(-256, 256))
            });
            this.map.attributionControl.addAttribution('map by <a href="https://github.com/milan60" target="_blank">milan60</a>, cayo-perico by Spitfire2k6');

            L.tileLayer("https://cdn.celestial.network/tiles_" + Bounds.version + "/{z}/{x}/{y}.jpg", {
                noWrap: true,
                bounds: [
                    [0, 0],
                    [-256, 256],
                ],
            }).addTo(this.map);

            this.map.setView([-124, 124], 3);

            L.control.layers({}, this.layers).addTo(this.map);

            $.each(this.layers, function (key) {
                _this.layers[key].addTo(_this.map);
            });

            $.each(this.blips, function (_, blip) {
                const coords = eval('(() => (' + blip.coords + '))()'),
                    coordsText = coords.x.toFixed(2) + ' ' + coords.y.toFixed(2),
                    location = Vector3.fromGameCoords(coords.x, coords.y, 0);

                let marker = L.marker(location.toMap(),
                    {
                        icon: new L.Icon(
                            {
                                iconUrl: '/images/icons/' + blip.icon,
                                iconSize: [22, 22]
                            }
                        ),
                        forceZIndex: 99
                    }
                );

                marker.bindPopup(blip.label + '<br><i>' + coordsText + '</i>', {
                    autoPan: false
                });

                _this.layers["Blips"].addLayer(marker);
            });

            //this.__debugLocations(require('../../data/tp_locations.json'));

            this.map.on('click', function (e) {
                const coords = Vector3.fromMapCoords(e.latlng.lng, e.latlng.lat),
                    map = coords.toMap();

                _this.clickedCoords = "[X=" + Math.round(coords.x) + ",Y=" + Math.round(coords.y) + "] / [Lng=" + map.lng.toFixed(3) + ",Lat=" + map.lat.toFixed(3) + "]";
                _this.rawClickedCoords = {x: Math.round(coords.x), y: Math.round(coords.y)};
                _this.coordsCommand = "/tp_coords " + Math.round(coords.x) + " " + Math.round(coords.y);
            });

            this.map.on('dragstart', function () {
                _this.isDragging = true;
            });
            this.map.on('dragend', function () {
                _this.isDragging = false;
            });
            this.map.on('fullscreenchange', function () {
                setTimeout(function () {
                    _this.map._onResize();
                }, 500);
            });

            $('#map').on('contextmenu', 'img.leaflet-marker-icon', function (e) {
                e.preventDefault();

                const id = $(this).data('playerId');

                if (id) {
                    const player = _this.container.get(id);

                    if (player) {
                        _this.rightClickedPlayer.id = id;
                        _this.rightClickedPlayer.name = player.getTitle(true);
                        _this.rightClickedPlayer.playerName = player.player.name;
                        _this.rightClickedPlayer.tracked = player.isTracked();
                    }
                }
            });

            this.map.addControl(new L.Control.Fullscreen());

            $('#map-wrapper').on('click', '.track-cid', function (e) {
                e.preventDefault();

                const track = $(this).data('trackid');
                if (track === 'stop') {
                    window.location.hash = '';
                } else {
                    window.location.hash = track;
                    _this.firstRefresh = true;

                    _this.map.closePopup();

                    if ($(this).data('popup')) {
                        _this.openPopup = track;
                    }
                }
            });

            $('#map-wrapper').on('click', '.highlight-cid', function (e) {
                e.preventDefault();

                const steam = $(this).data('steam');
                if ($(this).hasClass('stop_highlight')) {
                    delete _this.highlightedPeople[steam];
                } else {
                    _this.highlightedPeople[steam] = true;
                }
            });

            const styles = [
                '.leaflet-marker-icon {transform-origin:center center !important;}',
                '.leaflet-grab {cursor:default;}',
                '.coordinate-attr {font-size: 11px;padding:0 5px;color:rgb(0, 120, 168);line-height:16.5px}',
                '.leaflet-control-layers-overlays {user-select:none !important}',
                '.leaflet-control-layers-selector {outline:none !important}',
                '.leaflet-container {background:#143D6B}',
                'path.leaflet-interactive[stroke="#FFBF00"] {cursor:default}',
                '.leaflet-attr {width:' + $('.leaflet-bottom.leaflet-right').width() + 'px}'
            ];
            $('#map').append('<style>' + styles.join('') + '</style>');
        }
    },
    mounted() {
        const _this = this;
        this.buildMap();

        $(document).ready(function () {
            $('#server').on('change', function () {
                _this.firstRefresh = true;

                _this.doMapRefresh($(this).val());
            }).trigger('change');
        });

        if (Math.round(Math.random() * 100) === 1) { // 1% chance it says fib spy satellite map
            $(document).ready(function () {
                $('#map_title').text(_this.t('map.spy_satellite'));
            });
        }

        instance = this;
    }
};
</script>
