<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white !mb-2">
                <span id="map_title">{{ t('map.title') }}</span>
                <select class="inline-block w-90 ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                        id="server">
                    <option v-for="server in servers" :key="server.name" :value="server.name">{{ server.name }}</option>
                </select>
                <select class="inline-block w-40 ml-2 mr-2 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                        v-model="selectedInstance">
                    <option v-for="instance in container.instances" :key="instance" :value="instance">
                        {{ instance === 1 ? t('map.main_instance') : t('map.instance', instance) }}
                    </option>
                </select>
            </h1>
            <p v-if="!isTimestampShowing && !isHistoricShowing">
                <span v-html="data" class="block"></span>
                <span class="block text-xxs text-muted dark:text-dark-muted mt-0 leading-3" v-if="lastConnectionError">
                    {{ lastConnectionError }}
                </span>
                <span class="block text-xs text-muted dark:text-dark-muted leading-3 mt-2">
                    <b>{{ t('map.current_viewers') }}: </b>
                    <span v-html="formatViewers()"></span>
                </span>
            </p>
        </portal>

        <portal to="actions">
            <div class="mb-2">
                <!-- Show Screenshot -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isScreenshot = true"
                    v-if="this.perm.check(this.perm.PERM_SCREENSHOT) && !isTimestampShowing && !isHistoricShowing">
                    <i class="fas fa-camera"></i>
                    {{ t('map.screenshot') }}
                </button>

                <!-- Show Timestamp -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isTimestamp = true"
                    v-if="this.perm.check(this.perm.PERM_ADVANCED)">
                    <i class="fas fa-vial"></i>
                    {{ t('map.timestamp_title') }}
                </button>

                <!-- Show Historic -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="showHistoric()"
                    v-if="this.perm.check(this.perm.PERM_ADVANCED)">
                    <i class="fas fa-map"></i>
                    {{ t('map.historic_title') }}
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
                    @click="isPaused = true" v-if="!isPaused && !isTimestampShowing && !isHistoricShowing">
                    <i class="fas fa-pause"></i>
                    {{ t('map.pause') }}
                </button>
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isPaused = false" v-if="isPaused && !isTimestampShowing && !isHistoricShowing">
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

        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k" v-if="isHistoric">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.historic_title') }}
                </h3>

                <!-- Steam Identifier -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/3 pt-2 font-bold" for="historic_steam">
                        {{ t('map.historic_steam') }}
                    </label>
                    <input class="w-2/3 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="historic_steam"
                           v-model="form.historic_steam"/>
                </div>

                <!-- From -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/3 pt-2 font-bold" for="historic_date_from">
                        {{ t('map.historic_from') }}
                    </label>
                    <input class="w-1/3 px-4 py-2 mr-1 bg-gray-200 dark:bg-gray-600 border rounded" type="date"
                           step="any" id="historic_date_from"
                           v-model="form.historic_from_date"/>
                    <input class="w-1/3 px-4 py-2 ml-1 bg-gray-200 dark:bg-gray-600 border rounded" type="time"
                           step="any" id="historic_time_from"
                           v-model="form.historic_from_time"/>
                </div>

                <!-- Till -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/3 pt-2 font-bold" for="historic_date_till">
                        {{ t('map.historic_till') }}
                    </label>
                    <input class="w-1/3 px-4 py-2 mr-1 bg-gray-200 dark:bg-gray-600 border rounded" type="date"
                           step="any" id="historic_date_till"
                           v-model="form.historic_till_date"/>
                    <input class="w-1/3 px-4 py-2 ml-1 bg-gray-200 dark:bg-gray-600 border rounded" type="time"
                           step="any" id="historic_time_till"
                           v-model="form.historic_till_time"/>
                </div>

                <p>
                    {{ t('map.historic_note') }}
                </p>

                <!-- Buttons -->
                <div class="flex items-center mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="showHistory">
                        <i class="mr-1 fas fa-plus"></i>
                        {{ t('global.confirm') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isHistoric = false">
                        {{ t('global.cancel') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k" v-if="isTimestamp">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.timestamp_title') }}
                </h3>

                <!-- From -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/3 pt-2 font-bold" for="historic_steam">
                        {{ t('map.timestamp_date') }}
                    </label>
                    <input class="w-2/3 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                           v-model="form.timestamp"/>
                </div>

                <!-- Buttons -->
                <div class="flex items-center mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="showTimestamp">
                        <i class="mr-1 fas fa-plus"></i>
                        {{ t('global.confirm') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isTimestamp = false">
                        {{ t('global.cancel') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Screenshot -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k"
             v-if="isScreenshot && this.perm.check(this.perm.PERM_SCREENSHOT)">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                    {{ t('map.screenshot') }}
                </h3>

                <p v-if="screenshotError" class="text-danger dark:text-dark-danger font-semibold mb-3">
                    {{ screenshotError }}
                </p>

                <a v-if="screenshotImage" class="w-full" :href="screenshotImage" target="_blank">
                    <img :src="screenshotImage" alt="Screenshot" class="w-full"/>
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
                    <button class="px-5 py-2 rounded bg-primary dark:bg-dark-primary mr-2"
                            @click="isAttachingScreenshot = true"
                            v-if="screenshotImage && screenshotSteam">
                        {{ t('screenshot.title') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isScreenshot = false; screenshotImage = null; screenshotError = null; screenshotSteam = null">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <ScreenshotAttacher :close="screenshotAttached" :steam="screenshotSteam" :url="screenshotImage"
                            v-if="isAttachingScreenshot"/>

        <template>
            <div class="-mt-12 flex flex-wrap">
                <div class="w-map mr-10" id="map-wrapper">
                    <div v-if="historyRange.view" class="mb-3">
                        <div class="flex">
                            <button
                                class="px-2 py-1 mr-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(-20)">-20s
                            </button>
                            <button
                                class="px-2 py-1 mr-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(-5)">-5s
                            </button>
                            <button
                                class="px-2 py-1 mr-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(-1)">-1s
                            </button>

                            <input type="range" :min="historyRange.min" :max="historyRange.max" value="0"
                                   @change="historyRangeChange" @input="historyRangeChange" id="range-slider"
                                   class="w-full px-2 py-1 range bg-transparent"/>

                            <button
                                class="px-2 py-1 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(1)">+1s
                            </button>
                            <button
                                class="px-2 py-1 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(5)">+5s
                            </button>
                            <button
                                class="px-2 py-1 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary"
                                @click="historyRangeButton(20)">+20s
                            </button>
                        </div>
                        <p class="text-center">{{ historyRange.val }}</p>
                        <p class="text-center text-sm">{{ historicDetails }}</p>
                    </div>

                    <div class="w-full mb-2">
                        <canvas width="1160" height="140" v-if="historicChart" id="historicChart"></canvas>
                    </div>

                    <div class="flex flex-wrap justify-between mb-2 w-map max-w-full" v-if="!historicChart && !isTimestampShowing && !isHistoricShowing">
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
                                @click="addArea(false)">
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
                             v-if="clickedCoords"><span @click="copyText($event, clickedCoords)">{{
                                clickedCoords
                            }}</span> / <span
                            @click="copyText($event, coordsCommand)">{{ t('map.command') }}</span></pre>
                        <pre
                            class="w-map-gauge leaflet-attr bg-opacity-70 bg-white absolute bottom-attr2 right-0 z-1k p-2 text-gray-800 text-xs"
                            v-if="advancedTracking && container.isTrackedPlayerVisible"
                        >{{ tracking.data.advanced }}</pre>
                        <div
                            class="w-map-gauge leaflet-attr bg-opacity-70 bg-white absolute bottom-attr right-0 z-1k px-2 pt-2 pb-1 flex"
                            :class="{'hidden' : !advancedTracking || !container.isTrackedPlayerVisible}"
                            v-if="!isTimestampShowing && !isHistoricShowing"
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

                        <div v-if="rightClickedPlayer.id && !isTimestampShowing && !isHistoricShowing"
                             class="absolute z-1k top-0 left-0 right-0 bottom-0 bg-black bg-opacity-70">
                            <div
                                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded">
                                <h2 class="text-xl mb-2" v-html="rightClickedPlayer.name"></h2>
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
                            <img src="/images/icons/skull_red.png" class="w-map-icon inline-block"
                                 alt="dead passenger"/>
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
                </div>

                <!-- Detection Areas -->
                <div class="flex flex-wrap" v-if="detectionAreas.length > 0 && !isTimestampShowing && !isHistoricShowing">
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
                                    <span class="text-yellow-600"
                                          :title="t('map.invisible_time', formatSeconds(Math.round(player.invisible_time / 1000)))"
                                          v-if="player.invisible_time > 0">
                                        [I]
                                    </span>
                                    <a class="track-cid text-yellow-600" href="#"
                                       @click="trackServerId($event, 'server_' + player.source)"
                                       data-popup="true">
                                        {{ t('map.short.track') }}
                                    </a>
                                    <a class="highlight-cid text-yellow-600" href="#"
                                       @click="highlightServerId($event, player.steam)">
                                        {{ t('map.short.highlight') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="flex flex-wrap">
                    <!-- Invisible Players -->
                    <div v-if="invisiblePeople.length > 0 && !isTimestampShowing && !isHistoricShowing" class="pt-4 mr-4 font-medium">
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
                    <div v-if="Object.keys(highlightedPeople).length > 0 && !isTimestampShowing && !isHistoricShowing" class="pt-4 mr-4">
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
                                    <a class="track-cid" href="#" :data-trackid="'server_' + player.source"
                                       data-popup="true">
                                        {{ t('map.short.track') }}
                                    </a>
                                    <a href="#" @click="stopHighlight($event, steam)">{{ t('map.short.remove') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- AFK Players -->
                    <div v-if="afkPeople.length > 0 && !isTimestampShowing && !isHistoricShowing" class="pt-4 mr-4 font-medium">
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
                                       @click="trackServerId($event, 'server_' + player.source)"
                                       data-popup="true">{{
                                            t('map.short.track')
                                        }}</a>
                                    <a class="highlight-cid" :style="'color:' + player.color" href="#"
                                       @click="highlightServerId($event, player.steam)">{{ t('map.short.highlight') }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Notifications -->
                    <div v-if="!container.notifier.isEmpty() && !isTimestampShowing && !isHistoricShowing" class="pt-4">
                        <h3 class="mb-2">{{ t('map.notify') }}</h3>
                        <table class="text-sm font-mono font-medium">
                            <tr v-for="(player, steam) in container.notifier.notifications.invisible"
                                :key="'invisible_' + steam">
                                <td class="pr-2">
                                    <span class="dark:text-yellow-500 text-yellow-600"
                                          v-if="steam === '*'">* (any)</span>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else-if="player === true">{{
                                            steam
                                        }}</a>
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
                                    <span class="dark:text-yellow-500 text-yellow-600"
                                          v-if="steam === '*'">* (any)</span>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else-if="player === true">{{
                                            steam
                                        }}</a>
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
                                    <span class="dark:text-yellow-500 text-yellow-600"
                                          v-if="steam === '*'">* (any)</span>
                                    <a target="_blank" :href="'/players/' + steam"
                                       class="dark:text-green-400 text-green-600" v-else-if="player === true">{{
                                            steam
                                        }}</a>
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

                <div class="flex flex-wrap" v-if="!isTimestampShowing && !isHistoricShowing">
                    <simple-player-list
                        v-if="isShowingOnDutyList"
                        :title="t('map.duty_list_pd')"
                        :players="container.on_duty.pd"
                        color="text-map-police"
                        :track-server-id="trackServerId"
                        :highlight-server-id="highlightServerId"
                    ></simple-player-list>

                    <simple-player-list
                        v-if="isShowingOnDutyList"
                        :title="t('map.duty_list_ems')"
                        :players="container.on_duty.ems"
                        color="text-map-ems"
                        :track-server-id="trackServerId"
                        :highlight-server-id="highlightServerId"
                    ></simple-player-list>

                    <simple-player-list
                        :title="t('map.staff_online')"
                        :players="container.staff"
                        color="text-map-staff"
                        :usePlayerName="true"
                        :track-server-id="trackServerId"
                        :highlight-server-id="highlightServerId"
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
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

import moment from "moment";
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import SimplePlayerList from './../../Components/Map/SimplePlayerList';
import ScreenshotAttacher from './../../Components/ScreenshotAttacher';
import L from "leaflet";
import {GestureHandling} from "leaflet-gesture-handling";
import "leaflet-rotatedmarker";
import 'leaflet-fullscreen';
import 'leaflet.markercluster';
import 'leaflet.heat';
import VueSpeedometer from "vue-speedometer";

import {io} from "socket.io-client";

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

window.findPlayer = function () {
};

export default {
    layout: Layout,
    components: {
        VSection,
        VueSpeedometer,
        SimplePlayerList,
        ScreenshotAttacher,
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
        staffMap: {
            type: Array,
            required: true
        },
        blips: {
            type: Array,
            required: true
        },
        marker: {
            type: Array
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

                screenshotId: 0,

                timestamp: Math.floor(Date.now() / 1000),

                historic_steam: '',
                historic_from_date: '',
                historic_from_time: '',
                historic_till_date: '',
                historic_till_time: ''
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
            screenshotSteam: null,
            screenshotError: null,
            isAttachingScreenshot: false,

            heatmapLayers: [],
            historyMarker: null,
            loadingScreenStatus: null,

            isTimestamp: false,
            isHistoric: false,

            isTimestampShowing: false,
            isHistoricShowing: false,

            historicDetails: '',

            historicChart: false,

            historyRange: {
                view: false,
                min: 0,
                max: 1,
                val: 0,
                data: [],

                minAltitude: 0,
                maxAltitude: 0
            },

            activeViewers: [],

            selectedInstance: 1
        };
    },
    methods: {
        formatViewers() {
            const viewers = this.activeViewers.filter(v => !this.isFake(v));

            if (!viewers || viewers.length === 0) {
                return '-';
            }

            return viewers.map(v => this.getStaffName(v)).join(', ');
        },
        getStaffName(steam) {
            let player_name = steam;

            for (let x = 0; x < this.staffMap.length; x++) {
                const staff = this.staffMap[x];

                if (staff.steam_identifier === steam) {
                    player_name = staff.player_name;
                    break;
                }
            }

            const cls = this.container.players && steam in this.container.players ? 'dark:text-green-300 text-green-500' : 'dark:text-blue-300 text-blue-500',
                title = this.container.players && steam in this.container.players ? this.t('map.viewer_in_server') : this.t('map.viewer_not_server');

            return '<a href="/players/' + steam + '" target="_blank" title="' + title + '" class="!no-underline ' + cls + '">' + player_name + '</a>';
        },
        showHistoric() {
            const fromDate = this.$moment().subtract(1, 'hours'),
                tillDate = this.$moment();

            if (!this.form.historic_from_date) {
                this.form.historic_from_date = fromDate.format("YYYY-MM-DD");
            }
            if (!this.form.historic_till_date) {
                this.form.historic_till_date = tillDate.format("YYYY-MM-DD");
            }

            if (!this.form.historic_from_time) {
                this.form.historic_from_time = fromDate.format("HH:mm");
            }
            if (!this.form.historic_till_time) {
                this.form.historic_till_time = tillDate.format("HH:mm");
            }

            this.isHistoric = true;
        },
        isFake(steam) {
            const player = this.container.get(steam);

            return player && player.player && player.player.isFake;
        },
        screenshotAttached(status, message) {
            this.isAttachingScreenshot = false;

            if (message) {
                alert(message);
            }

            if (status) {
                this.isScreenshot = false;
                this.screenshotImage = null;
                this.screenshotError = null;
                this.screenshotSteam = null;
            }
        },
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
                return isDev ? 'ws://localhost:9999' : 'wss://' + window.location.host;
            } else {
                return isDev ? 'http://localhost:9999' : 'https://' + window.location.host;
            }
        },
        async createScreenshot() {
            if (this.isScreenshotLoading) {
                return;
            }
            this.isScreenshotLoading = true;
            this.screenshotError = null;

            this.screenshotImage = null;
            this.screenshotSteam = null;

            try {
                const result = await axios.post('/api/screenshot/' + $('#server').val() + '/' + this.form.screenshotId);
                this.isScreenshotLoading = false;

                if (result.data) {
                    if (result.data.status) {
                        console.info('Screenshot of ID ' + this.form.screenshotId, result.data.data.url, result.data.data.steam);

                        this.screenshotImage = result.data.data.url;
                        this.screenshotSteam = result.data.data.steam;
                    } else {
                        this.screenshotError = result.data.message ? result.data.message : this.t('map.screenshot_failed');
                    }
                }
            } catch (e) {
                this.screenshotError = this.t('map.screenshot_failed');
            }
        },
        historyRangeButton(move) {
            if (this.historyRange && this.historyMarker) {
                const newVal = parseInt($('#range-slider').val()) + move;

                $('#range-slider').val(Math.min(this.historyRange.max, Math.max(this.historyRange.min, newVal)));

                this.historyRangeChange();
            }
        },
        historyRangeChange(timestamp) {
            if (this.historyRange && this.historyMarker) {
                const val = Number.isInteger(timestamp) ? timestamp : $('#range-slider').val();

                let pos = this.historyRange.data[val];

                this.renderAltitudeChart(val);

                if (!pos) {
                    pos = this.historyRange.data[val - 1];

                    if (!pos) {
                        pos = this.historyRange.data[val + 1];
                    }
                }

                const timezone = new Date(val * 1000).toLocaleDateString('en-US', {
                    day: '2-digit',
                    timeZoneName: 'short',
                }).slice(4);

                let icon = "circle",
                    label = moment.unix(val).format("MM/DD/YYYY - h:mm:ss") + ' ' + timezone + ' (' + val + ')';

                const flags = [
                    pos && pos.i ? 'invisible' : false,
                    pos && pos.c ? 'invincible' : false,
                    pos && pos.f ? 'frozen' : false,
                    pos && pos.d ? 'dead' : false
                ].filter(flag => flag).join(", ");

                this.historicDetails = "Flags: " + (flags ? flags : 'N/A') + " - Altitude: " + (pos ? pos.z + "m" : "N/A");

                if (pos) {
                    const coords = Vector3.fromGameCoords(parseInt(pos.x), parseInt(pos.y), 0).toMap();

                    this.historyMarker.setLatLng([coords.lat, coords.lng]);

                    if (pos.i) {
                        icon = "circle_green";
                    }
                } else {
                    label += ' [no-data]';

                    icon = "circle_red";
                }

                this.historyRange.val = label;

                this.historyMarker.setIcon(new L.Icon(
                    {
                        iconUrl: '/images/icons/' + icon + '.png',
                        iconSize: [20, 20]
                    }
                ));
            }
        },
        getAltitudeChartColor(invincible, invisible, frozen, dead) {
            if (invincible && invisible && frozen) {
                return "#c567e4";
            } else if (invincible && invisible || invisible && frozen || invincible && frozen) {
                return "#e4c567";
            } else if (invisible) {
                return "#a6e467";
            } else if (invincible) {
                return "#ff99ff";
            } else if (frozen) {
                return "#99ccff";
            } else if (dead) {
                return "#002db3";
            }

            return "#8080ff";
        },
        renderAltitudeChart(timestamp) {
            const fromTime = parseInt(timestamp),
                tillTime = fromTime + 60;

            const canvas = document.getElementById("historicChart");

            if (canvas) {
                let data = [],
                    colors = [];

                for (let x = fromTime; x < tillTime; x++) {
                    let pos = this.historyRange.data[x];

                    if (!pos) {
                        pos = this.historyRange.data[x - 1];

                        if (!pos) {
                            pos = this.historyRange.data[x + 1];
                        }
                    }

                    const val = pos ? pos.z : null;

                    data.push(val);

                    colors.push(pos ? this.getAltitudeChartColor(pos.c, pos.i, pos.f, pos.d) : '#ff4d4d')
                }

                let lastValue = data[0];

                if (lastValue === null) {
                    const first = Object.keys(this.historyRange.data)[0];

                    for (let x = fromTime; x >= first; x--) {
                        const pos = this.historyRange.data[x];

                        if (pos) {
                            lastValue = pos.z;

                            break;
                        }
                    }

                    if (lastValue === null) {
                        for (let x = first; x >= tillTime; x++) {
                            const pos = this.historyRange.data[x];

                            if (pos) {
                                lastValue = pos.z;

                                break;
                            }
                        }
                    }
                }

                const cWidth = canvas.width - 2,
                    cHeight = canvas.height - 2;

                const min = this.historyRange.minAltitude,
                    max = this.historyRange.maxAltitude - min,
                    width = cWidth / data.length;

                const ctx = canvas.getContext('2d');

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let x = 0; x < data.length; x++) {
                    const value = data[x] ? data[x] : lastValue;

                    const normalizedValue = value ? (max - (value - min)) / max : 0;

                    if (x > 0) {
                        const x2 = x * width,
                            y2 = Math.max(
                                Math.min(
                                    (normalizedValue * cHeight) + 1,
                                    canvas.height - 1),
                                1);

                        ctx.lineTo(x2, y2);
                        ctx.closePath();

                        ctx.lineWidth = 2;
                        ctx.strokeStyle = colors[x];
                        ctx.stroke();

                        ctx.beginPath();
                        ctx.moveTo(x2, y2);
                    } else {
                        const x1 = 1,
                            y1 = value ? (normalizedValue * cHeight) + 1 : 1;

                        ctx.beginPath();
                        ctx.moveTo(x1, y1);
                    }

                    if (value) {
                        lastValue = value;
                    }
                }
            } else {
                setTimeout(() => {
                    this.renderAltitudeChart(timestamp);
                }, 100);
            }
        },
        async showTimestamp() {
            const timestamp = this.form.timestamp;

            if (timestamp && timestamp > 0 && timestamp < Date.now() / 1000) {
                this.isTimestamp = false;

                this.isHistoricShowing = false;
                this.isTimestampShowing = true;

                this.stopTracking();

                await this.renderTimestamp(timestamp);
            } else {
                alert('Invalid timestamp');
            }
        },
        async showHistory() {
            const fromUnix = this.$moment(this.form.historic_from_date + ' ' + this.form.historic_from_time).unix();
            const tillUnix = this.$moment(this.form.historic_till_date + ' ' + this.form.historic_till_time).unix();

            if (fromUnix && tillUnix) {
                if (this.form.historic_steam || !this.form.historic_steam.startsWith('steam:')) {
                    this.isHistoric = false;

                    this.isHistoricShowing = true;
                    this.isTimestampShowing = false;

                    this.stopTracking();

                    await this.renderHistory(this.form.historic_steam.replace('steam:', ''), fromUnix, tillUnix);
                } else {
                    alert('Invalid steam identifier');
                }
            } else {
                alert('Invalid from / till');
            }
        },
        async renderHistory(steam, from, till) {
            if (this.loadingScreenStatus) {
                return;
            }
            this.loadingScreenStatus = this.t('map.heatmap_fetch');

            this.historicChart = false;

            const server = $('#server').val(),
                history = await this.loadHistory(server, steam, from, till);

            this.loadingScreenStatus = this.t('map.heatmap_render');

            if (this.heatmapLayers) {
                for (let x = 0; x < this.heatmapLayers.length; x++) {
                    this.map.removeLayer(this.heatmapLayers[x]);
                }

                if (this.historyMarker) {
                    this.map.removeLayer(this.historyMarker);
                }

                this.heatmapLayers = [];
            }

            this.historyRange.view = false;

            if (history) {
                this.historicChart = true;

                $('.leaflet-control-layers-selector').each(function () {
                    if ($(this).prop('checked')) {
                        $(this).trigger('click');
                    }
                });

                const timestamps = Object.keys(history);

                const first = timestamps[0],
                    last = timestamps[timestamps.length - 1];

                const addPolyline = (coords) => {
                    let line = L.polyline(coords, {color: '#3380f3'});

                    line.on('click', (e) => {
                        const latlng = e.latlng;

                        let closestDistance = 1000;
                        let closest = false;

                        Object.entries(history).forEach(entrySet => {
                            const coords = Vector3.fromGameCoords(parseInt(entrySet[1].x), parseInt(entrySet[1].y), 0).toMap();

                            const dst = distance(coords.lat, coords.lng, latlng.lat, latlng.lng);

                            if (dst < closestDistance) {
                                closestDistance = dst;
                                closest = entrySet[0];
                            }
                        });

                        $('#range-slider').val(closest);

                        this.historyRangeChange();
                    });

                    line.addTo(this.map);

                    this.heatmapLayers.push(line);
                }

                let latlngs = [],
                    lastEntryNull = 0;

                for (let x = first; x <= last; x++) {
                    let pos = history[x];

                    if (!pos) {
                        pos = history[x - 1];

                        if (!pos) {
                            pos = history[x + 1];
                        }
                    }

                    if (pos) {
                        if (lastEntryNull >= 10 && pos.i && pos.c && pos.f) {
                            continue;
                        }

                        const coords = Vector3.fromGameCoords(pos.x, pos.y, 0).toMap();

                        latlngs.push([coords.lat, coords.lng]);

                        lastEntryNull = 0;
                    } else {
                        if (latlngs.length > 0) {
                            addPolyline(latlngs);

                            latlngs = [];
                        }

                        lastEntryNull++;
                    }
                }

                if (latlngs.length > 0) {
                    addPolyline(latlngs);
                }

                function distance(x1, y1, x2, y2) {
                    const xDiff = x1 - x2;
                    const yDiff = y1 - y2;

                    return Math.abs(Math.sqrt(xDiff * xDiff + yDiff * yDiff));
                }

                this.historyMarker = L.marker(latlngs[0], {});

                this.historyMarker.setIcon(new L.Icon(
                    {
                        iconUrl: '/images/icons/circle.png',
                        iconSize: [20, 20]
                    }
                ));

                const temp = Object.values(history).map(entry => entry.z);

                this.historyRange.minAltitude = Math.min(...temp);
                this.historyRange.maxAltitude = Math.max(...temp);

                this.historyRange.val = (new Date(timestamps[0] * 1000)).toGMTString() + ' (' + timestamps[0] + ')';
                this.historyRange.min = timestamps[0];
                this.historyRange.max = timestamps[timestamps.length - 1];

                console.log(timestamps[0]);

                this.historyRange.data = history;

                this.historyRange.view = true;

                this.historyMarker.addTo(this.map);

                //this.map.fitBounds(this.heatmapLayer.getBounds());

                this.historyRangeChange(parseInt(timestamps[0]));
            }

            this.loadingScreenStatus = null;
        },
        async loadHistory(server, steam, from, till) {
            this.loadingScreenStatus = this.t('map.heatmap_fetch');
            try {
                const result = await axios.get(this.hostname(false) + '/historic/' + server + '/' + steam + '/' + from + '/' + till + '?token=' + this.token);

                this.loadingScreenStatus = this.t('map.heatmap_parse');
                if (result.data && result.data.status) {
                    return result.data.data;
                } else if (result.data && !result.data.status) {
                    console.error(result.data.error);

                    alert(result.data.error);
                }
            } catch (e) {
                console.error(e);
            }

            return null;
        },
        async renderTimestamp(timestamp) {
            this.historicChart = false;
            this.historyRange.view = false;

            if (this.loadingScreenStatus) {
                return;
            }
            this.loadingScreenStatus = this.t('map.timestamp_fetch');

            const server = $('#server').val(),
                players = await this.loadTimestamp(server, timestamp);

            this.loadingScreenStatus = this.t('map.timestamp_render');

            if (players) {
                if (this.heatmapLayers) {
                    for (let x = 0; x < this.heatmapLayers.length; x++) {
                        this.map.removeLayer(this.heatmapLayers[x]);
                    }

                    if (this.historyMarker) {
                        this.map.removeLayer(this.historyMarker);
                    }

                    this.heatmapLayers = [];
                }

                $('.leaflet-control-layers-selector').each(function () {
                    if ($(this).prop('checked')) {
                        $(this).trigger('click');
                    }
                });

                const cluster = L.markerClusterGroup({
                    maxClusterRadius: 10
                });

                this.heatmapLayers.push(cluster);

                cluster.addTo(this.map);

                for (let x = 0; x < players.length; x++) {
                    const player = players[x];

                    const location = Vector3.fromGameCoords(player.x, player.y, 0.0);

                    let marker = L.marker(location.toMap(),
                        {
                            icon: new L.Icon(
                                {
                                    iconUrl: `/images/icons/${player.i ? 'circle_green' : 'circle'}.png`,
                                    iconSize: [17, 17]
                                }
                            ),
                            forceZIndex: 99
                        }
                    );

                    marker.bindPopup('<a href="/players/' + player.steam + '" target="_blank">' + player.steam + '</a>', {
                        autoPan: false
                    });

                    cluster.addLayer(marker);
                }
            }

            this.loadingScreenStatus = null;
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
                if (this.historyMarker) {
                    this.map.removeLayer(this.historyMarker);
                }
                this.heatmapLayer = null;
            }

            this.historyRange.view = false;

            if (heatmap) {
                $('.leaflet-control-layers-selector').each(function () {
                    if ($(this).prop('checked')) {
                        $(this).trigger('click');
                    }
                });

                this.heatmapLayer = L.heatLayer(heatmap, {
                    radius: 10,
                    minOpacity: 0.65,
                    maxZoom: 5,
                    max: 100,
                    blur: 15
                });

                this.heatmapLayer.addTo(this.map);
            }

            this.loadingScreenStatus = null;
        },
        async loadTimestamp(server, timestamp) {
            try {
                const result = await axios.get(this.hostname(false) + '/timestamp/' + server + '/' + timestamp + '?token=' + this.token);

                this.loadingScreenStatus = this.t('map.timestamp_parse');
                if (result.data && result.data.status) {
                    let players = [];

                    for (const steam in result.data.data) {
                        if (Object.hasOwnProperty(steam)) continue;

                        const coords = result.data.data[steam];

                        players.push({
                            steam: "steam:" + steam.replace(".csv", ""),
                            x: coords.x,
                            y: coords.y,
                            i: coords.i
                        });
                    }

                    return players;
                } else if (result.data && !result.data.status) {
                    alert(result.data.error);
                    console.error(result.data.error);
                }
            } catch (e) {
                console.error(e);
            }

            return null;
        },
        async loadHeatMap(server, date) {
            this.loadingScreenStatus = this.t('map.heatmap_fetch');
            try {
                const result = await axios.get(this.hostname(false) + '/history/heatmap/' + server + '/' + date + '?token=' + this.token + '&cluster=' + this.cluster);

                this.loadingScreenStatus = this.t('map.heatmap_parse');
                if (result.data && result.data.status) {
                    let heatmap = [];
                    for (const coords in result.data.data) {
                        if (Object.hasOwnProperty(coords)) continue;
                        const tmp = coords.split('/'),
                            location = Vector3.fromGameCoords(parseInt(tmp[0]), parseInt(tmp[1]), 0).toMap();

                        heatmap.push([
                            location.lat,
                            location.lng,
                            result.data.data[coords]
                        ]);
                    }

                    return heatmap;
                } else if (result.data && !result.data.status) {
                    console.error(result.data.error);
                }
            } catch (e) {
                console.error(e);
            }

            return null;
        },
        async initializeMap(server) {
            try {
                const connection = io(this.hostname(true), {
                    reconnectionDelayMax: 5000,
                    query: {
                        server: server,
                        token: this.token,
                        type: "world",
                        steam: this.$page.auth.player.steamIdentifier
                    }
                });

                connection.on("message", async (buffer) => {
                    try {
                        const unzipped = await DataCompressor.GUnZIP(buffer),
                            data = JSON.parse(unzipped);

                        await this.renderMapData(data);

                        this.firstRefresh = false;
                    } catch (e) {
                        console.error('Failed to parse socket message ', e);
                    }
                });

                connection.on("disconnect", async () => {
                    this.data = this.t('map.closed_expected', server);
                });
            } catch (e) {
                this.data = this.t('map.closed_unexpected', server);

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
            if (this.isPaused || this.isDragging || this.isTimestampShowing || this.isHistoricShowing) {
                return;
            }

            if (this.container.isTrackedPlayerVisible) {
                this.map.dragging.disable();
            } else {
                this.map.dragging.enable();
            }

            data = DataCompressor.decompressData(data);

            if (data && data.players.length > 0) {
                if (this.map) {
                    const _this = this;

                    this.container.updatePlayers(data.players, this, this.selectedInstance);

                    let unknownCharacters = [],
                        foundTracked = false;

                    this.container.eachPlayer(function (id, player) {
                        if (window.findPlayer) {
                            window.findPlayer(player);
                        }

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

                            if (player.afk.time > 10) {
                                trackingInfo.push('AFK since ' + _this.$options.filters.humanizeSeconds(player.afk.time));
                            }

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

                    for (let x = 0; x < this.detectionAreas.length; x++) {
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

                this.activeViewers = data.viewers.sort();
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
            this.map.attributionControl.addAttribution('map by <a href="https://github.com/twooot" target="_blank">Laura</a> <i>accurate to about 1-2m</i>');

            L.tileLayer("https://worryfree.host/tiles/tiles_" + Bounds.version + "/{z}/{x}/{y}.png", {
                noWrap: true,
                bounds: [
                    [0, 0],
                    [-256, 256],
                ],
            }).addTo(this.map);

            this.map.setView([-159.287, 124.773], 3);

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

            if (this.marker) {
                const location = Vector3.fromGameCoords(this.marker[0], this.marker[1], 0);

                let marker = L.marker(location.toMap(),
                    {
                        icon: new L.Icon(
                            {
                                iconUrl: '/images/icons/marker.png',
                                iconSize: [22, 22]
                            }
                        ),
                        forceZIndex: 99
                    }
                );

                marker.bindPopup(this.t('map.marker', this.marker[0] + ", " + this.marker[1]), {
                    autoPan: false
                });

                marker.addTo(this.map);

                this.map.setView(location.toMap(), 8);

                marker.openPopup();
            }

            //this.__debugLocations(require('../../data/tp_locations.json'));

            this.map.on('click', function (e) {
                const coords = Vector3.fromMapCoords(e.latlng.lng, e.latlng.lat),
                    map = coords.toMap();

                if (Bounds.calibrating) {
                    _this.copyText(null, `lng: ${e.latlng.lng}, lat: ${e.latlng.lat}`);
                }

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
        },
        trackServerId(event, track) {
            event.preventDefault();

            if (track === 'stop') {
                window.location.hash = '';
            } else {
                window.location.hash = track;
                this.firstRefresh = true;

                this.map.closePopup();

                if ($(this).data('popup')) {
                    this.openPopup = track;
                }
            }
        },
        highlightServerId(event, steam) {
            event.preventDefault();

            if ($(this).hasClass('stop_highlight')) {
                delete this.highlightedPeople[steam];
            } else {
                this.highlightedPeople[steam] = true;
            }
        }
    },
    mounted() {
        const _this = this;
        this.buildMap();

        $(document).ready(function () {
            $('#server').on('change', function () {
                _this.firstRefresh = true;

                _this.initializeMap($(this).val());
            }).trigger('change');
        });

        if (Math.round(Math.random() * 100) === 1) { // 1% chance it says fib spy satellite map
            $(document).ready(function () {
                $('#map_title').text(_this.t('map.spy_satellite'));
            });
        }

        window.renderHeatMap = (date) => {
            this.renderHeatMap(date);
        };

        window.renderTimestamp = (timestamp) => {
            this.renderTimestamp(timestamp);
        };

        window.instance = this;
    }
};
</script>
