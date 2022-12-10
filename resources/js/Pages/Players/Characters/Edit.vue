<template>
    <div>

        <portal to="title">
            <div class="flex items-start space-x-10 mobile:flex-wrap">
                <h1 class="dark:text-white">
                    {{ character.name }} #{{ character.id }}
                </h1>
                <div
                    class="flex items-center space-x-5 mobile:flex-wrap mobile:w-full mobile:!mr-0 mobile:!ml-0 mobile:space-x-0">
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale"
                           v-if="character.characterDeleted">
                        <span class="font-semibold">
                            {{ t('players.edit.deleted') }}:
                            {{ $moment(character.characterDeletionTimestamp).format('l') }}
                        </span>
                    </badge>
                </div>
            </div>
        </portal>

        <portal to="actions">
            <div>
                <!-- Remove Tattoos -->
                <a href="#"
                   class="px-5 py-2 font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                   @click="function(e) {e.preventDefault(); isTattooRemoval = true}">
                    <i class="fas fa-eraser"></i>
                    {{ t('players.characters.remove_tattoo') }}
                </a>
                <!-- Reset Spawn-point -->
                <a href="#"
                   class="px-5 py-2 font-semibold text-white rounded bg-warning mr-3 dark:bg-dark-warning mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                   @click="function(e) {e.preventDefault(); isResetSpawn = true}">
                    <i class="fas fa-heartbeat"></i>
                    {{ t('players.characters.reset_spawn') }}
                </a>
                <!-- Back -->
                <a class="px-5 py-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                   :href="'/players/' + returnTo">
                    <i class="fas fa-backward"></i>
                    {{ t('global.back') }}
                </a>
            </div>
        </portal>

        <!-- Remove Tattoos -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isTattooRemoval">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.sure_tattoos') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold">
                        {{ t('players.characters.tattoo_zone') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="zone">
                        <option value="all">{{ t('players.characters.zone.all') }}</option>
                        <option value="head" selected>{{ t('players.characters.zone.head') }}</option>
                        <option value="left_arm">{{ t('players.characters.zone.left_arm') }}</option>
                        <option value="right_arm">{{ t('players.characters.zone.right_arm') }}</option>
                        <option value="torso">{{ t('players.characters.zone.torso') }}</option>
                        <option value="left_leg">{{ t('players.characters.zone.left_leg') }}</option>
                        <option value="right_leg">{{ t('players.characters.zone.right_leg') }}</option>
                    </select>
                </div>
                <p v-html="t('players.characters.tattoo_no_undo')">
                    {{ t('players.characters.tattoo_no_undo') }}
                </p>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isTattooRemoval = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger"
                            @click="removeTattoos">
                        {{ t('players.characters.tattoo_do') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Reset spawn -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isResetSpawn">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.sure_spawn') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold">
                        {{ t('players.characters.spawn_point') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="spawn">
                        <option v-for="coords in getResetCoords()" :key="coords.key" :value="coords.key">
                            {{ coords.label }}
                        </option>
                        <option value="staff" v-if="player.isStaff">
                            {{ t('players.characters.spawn.staff') }}
                        </option>
                    </select>
                </div>
                <p v-html="t('players.characters.spawn_no_undo')">
                    {{ t('players.characters.spawn_no_undo') }}
                </p>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isResetSpawn = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger"
                            @click="resetSpawn">
                        {{ t('players.characters.spawn_do') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Editing -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('global.info') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent="submit(false)">
                    <!-- Name & Phone -->
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="first_name">
                                {{ t('players.edit.prename') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="first_name" v-model="form.first_name">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="last_name">
                                {{ t('players.edit.surname') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="last_name" v-model="form.last_name">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="dob">
                                {{ t('players.edit.dob') }}
                            </label>
                            <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                   id="dob" v-model="form.date_of_birth">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2">
                                {{ t('players.edit.gender') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                    id="gender" v-model="form.gender">
                                <option value="0">{{ t('global.male') }}</option>
                                <option value="1">{{ t('global.female') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-3 mb-6">
                        <label class="block mb-3">
                            {{ t('players.edit.backstory') }}
                        </label>
                        <textarea class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                  id="backstory" v-model="form.backstory"></textarea>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-600">

                    <div class="flex flex-wrap mb-6 mt-6">
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block font-semibold">
                                {{ t('players.characters.license.licenses') }}
                            </label>
                            <ul v-if="character.licenses.length > 0" class="text-sm">
                                <li v-for="license in character.licenses" :key="license" class="ml-3 pl-2 list-dash">
                                    {{ t('players.characters.license.' + license) }}
                                </li>
                            </ul>
                            <ul v-else class="text-sm">
                                <li class="ml-3 pl-2 list-dash">{{ t('global.none') }}</li>
                            </ul>

                            <!-- Add License -->
                            <button type="button"
                                    class="block w-full px-5 py-2 mt-6 hover:shadow-xl font-semibold text-white rounded bg-primary mr-3 dark:bg-dark-primary"
                                    @click="isLicenceAdd = true">
                                {{ t('players.characters.license.add') }}
                            </button>
                        </div>
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <table class="text-left w-full">
                                <tr>
                                    <th class="font-semibold p-2">{{ t('players.edit.phone') }}</th>
                                    <td class="p-2">
                                        <span class="block border-gray-500 border-b-2 px-3 py-2">
                                            {{ character.phoneNumber }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="italic p-2 font-normal" colspan="2">{{ t('players.edit.outfits', character.outfits) }}</th>
                                </tr>
                            </table>
                        </div>
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3 relative">
                            <table class="text-left w-full">
                                <tr>
                                    <th class="p-2">
                                        <label class="block font-semibold">
                                            {{ t('players.characters.edit_cash') }}
                                        </label>
                                    </th>
                                    <td class="p-2">
                                        <input type="number"
                                               class="block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                               v-model="balanceForm.cash" v-if="$page.auth.player.isSuperAdmin"/>
                                        <span class="block border-gray-500 border-b-2 px-3 py-2"
                                              v-else>{{ numberFormat(balanceForm.cash, 0, true) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="p-2">
                                        <label class="block font-semibold">
                                            {{ t('players.characters.edit_bank') }}
                                        </label>
                                    </th>
                                    <td class="p-2">
                                        <input type="number"
                                               class="block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                               v-model="balanceForm.bank" v-if="$page.auth.player.isSuperAdmin"/>
                                        <span class="block border-gray-500 border-b-2 px-3 py-2"
                                              v-else>{{ numberFormat(balanceForm.bank, 0, true) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="p-2">
                                        <label class="block font-semibold">
                                            {{ t('players.characters.edit_stocks') }}
                                        </label>
                                    </th>
                                    <td class="p-2">
                                        <input type="number"
                                               class="block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                               v-model="balanceForm.stocks" v-if="$page.auth.player.isSuperAdmin"/>
                                        <span class="block border-gray-500 border-b-2 px-3 py-2"
                                              v-else>{{ numberFormat(balanceForm.stocks, 0, true) }}</span>
                                    </td>
                                </tr>
                                <tr v-if="$page.auth.player.isSuperAdmin">
                                    <td class="p-2" colspan="2">
                                        <button type="button"
                                                class="block w-full px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-warning mr-3 dark:bg-dark-warning"
                                                @click="editBalance">
                                            {{ t('players.characters.balance_do') }}
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-600">

                    <!-- Submit -->
                    <div class="px-3 mt-6">
                        <button
                            class="px-5 py-3 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400 w-1/4"
                            type="submit">
                            {{ t('players.edit.update') }}
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Job -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.job.job') }}
                </h2>
            </template>

            <template>
                <div class="flex flex-wrap mb-4">
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3">
                            {{ t('players.job.name') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="job"
                                v-model="form.job_name" @change="setPayCheck">
                            <option value="Unemployed">Unemployed</option>

                            <option :value="job.name" v-for="job in formattedJobs">{{ job.name || t('global.none') }}</option>
                        </select>
                    </div>
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3" v-if="form.job_name === job.name"
                         v-for="job in formattedJobs">
                        <label class="block mb-3">
                            {{ t('players.job.department') }}
                        </label>
                        <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                id="department" v-model="form.department_name" @change="setPayCheck">
                            <option :value="null" v-if="form.job_name === 'Unemployed'">{{ t('global.none') }}</option>

                            <option :value="department.name" v-for="department in job.departments">
                                {{ department.name || t('global.none') }}
                            </option>
                        </select>
                    </div>
                    <template v-if="form.job_name === job.name" v-for="job in formattedJobs">
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3"
                             v-if="form.department_name === department.name" v-for="department in job.departments">
                            <label class="block mb-3">
                                {{ t('players.job.position') }}
                            </label>
                            <select class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                                    id="position" v-model="form.position_name" @change="setPayCheck">
                                <option :value="null" v-if="form.job_name === 'Unemployed'">{{ t('global.none') }}</option>

                                <option :value="position" v-for="position in department.positions">
                                    {{ position || t('global.none') }}
                                </option>
                            </select>
                        </div>
                    </template>
                    <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                        <label class="block mb-3">&nbsp;</label>
                        <button
                            class="block w-full px-4 py-3 mb-3 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            @click="updateJob">
                            {{ t('players.job.set') }} (${{ paycheck }})
                        </button>
                    </div>
                </div>
            </template>
        </v-section>

        <!-- Vehicle Editing -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isVehicleEdit">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.vehicle.edit') }}</h3>
                <p class="text-danger dark:text-dark-danger font-semibold mt-2 mb-2" v-if="vehicleEditError" id="vehicleEditError">{{ vehicleEditError }}</p>
                <div class="w-full mb-6">
                    <table class="text-left w-full">
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.owner') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                    min="0" v-model="vehicleForm.owner_cid"/>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.plate') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                    minlength="3" maxlength="8" v-model="vehicleForm.plate"/>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.repair') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.repair">
                                    <option value="fix">{{ t('players.characters.vehicle.repair_fix') }}</option>
                                    <option value="break">{{ t('players.characters.vehicle.repair_break') }}</option>
                                    <option :value="false">{{ t('players.characters.vehicle.repair_false') }}</option>
                                </select>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.fuel') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="100" step="0.1" v-model="vehicleForm.fuel"/>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <div class="w-full mb-6">
                    <table class="text-left w-full">
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.neon_enabled') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.neon_enabled">
                                    <option :value="true">{{ t('global.yes') }}</option>
                                    <option :value="false">{{ t('global.no') }}</option>
                                </select>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.xenon_headlights') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.xenon_headlights">
                                    <option :value="true">{{ t('global.yes') }}</option>
                                    <option :value="false">{{ t('global.no') }}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.tire_smoke') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input
                                    class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                    type="color" v-model="vehicleForm.modifications.tire_smoke"/>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.neon') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input
                                    class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                    type="color" v-model="vehicleForm.modifications.neon"/>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.turbo') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.turbo">
                                    <option :value="true">{{ t('global.yes') }}</option>
                                    <option :value="false">{{ t('global.no') }}</option>
                                </select>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.horn') }}
                                    <sup>
                                        <a class="dark:text-blue-300 text-blue-500" href="https://gta.fandom.com/wiki/Los_Santos_Customs/Horns" :title="t('players.characters.vehicle.horn_title')" target="_blank">[?]</a>
                                    </sup>
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.horn">
                                    <optgroup :label="group" v-for="(hornList, group) in horns">
                                        <option :value="horn.index" v-for="horn in hornList">{{ horn.label }}</option>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.engine') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="5" v-model="vehicleForm.modifications.engine"/>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.transmission') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="3" v-model="vehicleForm.modifications.transmission"/>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.breaks') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="3" v-model="vehicleForm.modifications.breaks"/>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.suspension') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="4" v-model="vehicleForm.modifications.suspension"/>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.armor') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <input type="number"
                                       class="w-28 block outline-none shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent"
                                       min="0" max="5" v-model="vehicleForm.modifications.armor"/>
                            </td>
                        </tr>
                        <tr>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.tint') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.tint">
                                    <option :value="0">{{ t('players.characters.vehicle.tints.0') }}</option>
                                    <option :value="1">{{ t('players.characters.vehicle.tints.1') }}</option>
                                    <option :value="2">{{ t('players.characters.vehicle.tints.2') }}</option>
                                    <option :value="3">{{ t('players.characters.vehicle.tints.3') }}</option>
                                    <option :value="4">{{ t('players.characters.vehicle.tints.4') }}</option>
                                    <option :value="5">{{ t('players.characters.vehicle.tints.5') }}</option>
                                </select>
                            </td>
                            <th class="p-2">
                                <label class="block font-semibold">
                                    {{ t('players.characters.vehicle.plate_type') }}
                                </label>
                            </th>
                            <td class="p-2">
                                <select
                                    class="w-28 block shadow-none !border-gray-500 border-0 border-b-2 bg-transparent !ring-transparent dark:bg-gray-600"
                                    v-model="vehicleForm.modifications.plate_type">
                                    <option :value="0">{{ t('players.characters.vehicle.plates.0') }}</option>
                                    <option :value="3">{{ t('players.characters.vehicle.plates.3') }}</option>
                                    <option :value="4">{{ t('players.characters.vehicle.plates.4') }}</option>
                                    <option :value="2">{{ t('players.characters.vehicle.plates.2') }}</option>
                                    <option :value="1">{{ t('players.characters.vehicle.plates.1') }}</option>
                                    <option :value="5">{{ t('players.characters.vehicle.plates.5') }}</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="flex justify-end">
                    <button type="button"
                            class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isVehicleEdit = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-success mr-3 dark:bg-dark-success"
                            @click="editVehicle">
                        <span v-if="!isVehicleLoading">
                            {{ t('players.characters.vehicle.confirm') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Vehicle Adding -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isVehicleAdd">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.vehicle.add') }}</h3>
                <div class="w-full p-3 flex justify-between">
                    <label class="mr-4 block w-1/3 text-center pt-2 font-bold">
                        {{ t('players.characters.vehicle.model') }}
                    </label>
                    <model-select
                        class="block w-2/3 px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                        :options="vehicleList"
                        v-model="vehicleAdd"/>
                </div>
                <div class="flex justify-end">
                    <button type="button"
                            class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isVehicleAdd = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-success mr-3 dark:bg-dark-success"
                            @click="addVehicle">
                        {{ t('players.characters.vehicle.add') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Add License -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isLicenceAdd">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.characters.license.add') }}</h3>
                <div class="w-full p-3 flex justify-between" v-if="licenses.length > 0">
                    <label class="mr-4 block w-1/3 text-center pt-2 font-bold">
                        {{ t('players.characters.license.license') }}
                    </label>
                    <select class="block w-2/3 px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600"
                            v-model="licenseForm.license">
                        <option :value="license" v-for="license in licenses">
                            {{ t('players.characters.license.' + license) }}
                        </option>
                    </select>
                </div>
                <p v-else>{{ t('players.characters.license.all') }}</p>
                <div class="flex justify-end">
                    <button type="button"
                            class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isLicenceAdd = false">
                        {{ t('global.cancel') }}
                    </button>
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-danger mr-3 dark:bg-dark-danger"
                            @click="removeLicenses">
                        {{ t('players.characters.license.remove') }}
                    </button>
                    <button type="button" v-if="licenses.length > 0"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-success mr-3 dark:bg-dark-success"
                            @click="addLicense">
                        {{ t('players.characters.license.add') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Vehicles -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.vehicles.vehicles') }}

                    <!-- Add Vehicle -->
                    <button
                        class="px-3 py-2 font-semibold text-white rounded bg-success dark:bg-dark-success text-base ml-5"
                        @click="isVehicleAdd = true"
                        v-if="$page.auth.player.isSuperAdmin"
                    >
                        <i class="fas fa-car"></i>
                        {{ t('players.characters.vehicle.add') }}
                    </button>
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-9">
                    <card
                        :key="vehicle.id"
                        v-for="(vehicle) in character.vehicles"
                        class="relative"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{
                                    vehicle.model_name in vehicleMap ? vehicleMap[vehicle.model_name] : vehicle.model_name
                                }}
                                <sup>{{ vehicle.model_name }}</sup>
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.vehicles.plate') }}:</span> {{ vehicle.plate }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.vehicles.vehicle_id') }}:</span> {{ vehicle.id }}
                            </h4>
                        </template>

                        <template>
                            <p v-html="t('players.vehicles.parked', vehicle.garage_name)">
                                {{ t('players.vehicles.parked', vehicle.garage_name) }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/vehicle/' + vehicle.id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-warning dark:bg-dark-warning rounded"
                                @click="startEditVehicle($event, vehicle)"
                                href="#"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-trash-alt mr-1"></i>
                                {{ t('players.characters.vehicle.confirm') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-red-600 dark:bg-red-400 rounded"
                                @click="deleteVehicle($event, vehicle.id)"
                                href="#"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-trash-alt mr-1"></i>
                                {{ t('global.delete') }}
                            </inertia-link>

                            <button
                                class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute top-1 left-1 bg-blue-700 dark:bg-blue-800 rounded"
                                :title="t('players.characters.vehicle.pd_emergency')"
                                v-if="vehicle.emergency === 1"
                            >
                                <i class="fas fa-car-alt"></i>
                            </button>
                            <button
                                class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute top-1 left-1 bg-pink-700 dark:bg-pink-800 rounded"
                                :title="t('players.characters.vehicle.ems_emergency')"
                                v-else-if="vehicle.emergency === 2"
                            >
                                <i class="fas fa-ambulance"></i>
                            </button>

                            <inertia-link
                                class="block px-2 py-1 text-center text-white absolute top-1 right-10 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventory_find/trunk/' + vehicle.id"
                                :title="t('inventories.show_trunk')"
                            >
                                <i class="fas fa-car-side"></i>
                            </inertia-link>
                            <inertia-link
                                class="block px-2 py-1 text-center text-white absolute top-1 right-1 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventory_find/glovebox/' + vehicle.id"
                                :title="t('inventories.show_glovebox')"
                            >
                                <i class="fas fa-car"></i>
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.vehicles.length === 0">
                    {{ t('players.vehicles.none') }}
                </p>
            </template>
        </v-section>

        <!-- Properties -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.properties.properties') }}
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        :key="property.id"
                        v-for="(property) in character.properties"
                        :no_body="true"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ property.property_address }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.cost') }}:</span>
                                {{ numberFormat(property.property_cost, 0, true) }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.rent') }}:</span>
                                {{ numberFormat(property.property_income, 0, true) }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.expires') }}:</span>
                                {{ (property.property_last_pay + (7 * 24 * 60 * 60)) * 1000 | formatTime(true) }}
                            </h4>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 mt-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/property/' + property.property_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.properties.length === 0">
                    {{ t('players.properties.none') }}
                </p>
            </template>

            <template v-if="$page.auth.player.isSeniorStaff">
                <h3 class="mb-4 mt-5 pt-5 border-t-2 border-dashed border-gray-500">
                    {{ t('players.properties.properties_shared') }}
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9" v-if="character.accessProperties.length > 0">
                    <card
                        :key="property.id"
                        v-for="(property) in character.accessProperties"
                        :no_body="true"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ property.property_address }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.properties.access_level') }}:</span>
                                {{ property.keys["c_" + character.id] || "N/A" }}
                            </h4>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 mt-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/property/' + property.property_id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="character.accessProperties.length === 0">
                    {{ t('players.properties.none_access') }}
                </p>
            </template>
        </v-section>

        <!-- Motels -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.motels.motels') }}
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        :key="motel.id"
                        v-for="(motel) in motels"
                        :no_body="true"
                        class="relative"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ motel.motel }} #{{ motel.room_id }}
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.motels.expires') }}:</span> {{ motel.expire | formatTime(true) }}
                            </h4>
                        </template>
                        <template #footer>
                            <inertia-link
                                class="block px-2 py-1 text-center text-white absolute top-1 right-1 bg-blue-600 dark:bg-blue-400 rounded"
                                v-if="motel.motel in motelMap"
                                :href="'/inventory/motel-' + motelMap[motel.motel] + '-' + motel.room_id + ':1'"
                                :title="t('inventories.show_motel')"
                            >
                                <i class="fas fa-archive"></i>
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 mt-3 text-center text-white bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/motel/' + motel.id"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="motels.length === 0">
                    {{ t('players.motels.none') }}
                </p>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../../Layouts/App';
import VSection from './../../../Components/Section';
import Card from './../../../Components/Card';
import Badge from './../../../Components/Badge';
import Modal from "../../../Components/Modal";
import {ModelSelect} from 'vue-search-select';
import axios from 'axios';

let jobsObject = [];

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
        Badge,
        Modal,
        ModelSelect,
    },
    props: {
        player: {
            type: Object,
            required: true,
        },
        character: {
            type: Object,
            required: true,
        },
        vehicleMap: {
            type: Array,
            required: true,
        },
        horns: {
            type: Object,
            required: true,
        },
        jobs: {
            type: Object,
            required: true,
        },
        motelMap: {
            type: Object,
            required: true,
        },
        motels: {
            type: Array,
            required: true,
        },
        resetCoords: {
            type: Array,
            required: true,
        },
        vehicleValue: {
            type: Number,
            required: true,
        },
        returnTo: {
            type: String,
            required: true,
        }
    },
    data() {
        jobsObject = [];

        for (const job in this.jobs) {
            if (Object.hasOwnProperty(job)) continue;

            let jobObject = {
                name: job,
                departments: []
            };

            for (const department in this.jobs[job]) {
                if (Object.hasOwnProperty(department)) continue;

                let departmentObject = {
                    name: department,
                    positions: {}
                };

                for (const position in this.jobs[job][department]) {
                    if (Object.hasOwnProperty(position)) continue;

                    departmentObject.positions[position] = this.jobs[job][department][position].salary;
                }

                jobObject.departments.push(departmentObject)
            }

            jobsObject.push(jobObject);
        }

        let jobs = JSON.parse(JSON.stringify(jobsObject.sort((a, b) => {
            return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
        })));

        for (let x = 0; x < jobs.length; x++) {
            let departments = jobs[x].departments.sort((a, b) => {
                return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
            });

            for (let y = 0; y < departments.length; y++) {
                departments[y].positions = Object.keys(departments[y].positions).reverse();
            }

            jobs[x].departments = departments;
        }

        let paychecks = {};
        for (let x = 0; x < jobsObject.length; x++) {
            const j = jobsObject[x];

            paychecks[j.name] = {};

            for (let y = 0; y < j.departments.length; y++) {
                const d = j.departments[y];

                paychecks[j.name][d.name] = d.positions;
            }
        }

        const sortedVehicles = this.vehicleMap.sort((a, b) => a.localeCompare(b)).map(m => {
            return {
                value: m,
                text: m
            };
        });

        const money = this.getMoneyLocals();

        return {
            local: {
                birth: this.t("players.edit.born", this.$moment(this.character.dateOfBirth).format('l')),
                cash: money.cash,
                cashTitle: money.cashTitle,
                stocks: money.stocks
            },
            paycheck: 0,
            form: {
                first_name: this.character.firstName,
                last_name: this.character.lastName,
                date_of_birth: this.character.dateOfBirth,
                gender: this.character.gender,
                backstory: this.character.backstory,
                job_name: this.character.jobName,
                department_name: this.character.departmentName,
                position_name: this.character.positionName,
            },
            vehicleList: sortedVehicles,
            vehicleAdd: {
                value: '',
                text: ''
            },
            location: window.location.href,
            vehicleForm: {
                id: 0,
                owner_cid: 0,
                fuel: 0.0,
                plate: '',
                modifications: {
                    xenon_headlights: false,
                    tire_smoke: '#ffffff',
                    neon_enabled: false,
                    engine: -1,
                    transmission: -1,
                    breaks: -1,
                    neon: '#ffffff',
                    turbo: false,
                    suspension: -1,
                    armor: -1,
                    plate_type: 0,
                    tint: 0,
                    horn: -1,
                },
                repair: false
            },
            licenseForm: {
                license: 'fw'
            },
            balanceForm: {
                cash: this.character.cash,
                bank: this.character.bank,
                stocks: this.character.stocksBalance
            },
            licenses: this.getAvailableLicenses(),
            isTattooRemoval: false,
            isResetSpawn: false,
            formattedJobs: jobs,
            paychecks: paychecks,
            isVehicleLoading: false,
            isVehicleEdit: false,
            vehicleEditError: null,
            isVehicleAdd: false,
            isLicenceAdd: false
        };
    },
    methods: {
        getResetCoords() {
            return this.resetCoords
                .map(coords => {
                    return {
                        label: this.t('players.characters.spawn.' + coords),
                        key: coords
                    }
                })
                .sort((a, b) => a.label.localeCompare(b.label));
        },
        getMoneyLocals() {
            return {
                cash: this.t("players.edit.cash", this.numberFormat(this.character.money, 0, true)),
                cashTitle: this.t(
                    "players.edit.cash_title",
                    this.numberFormat(this.character.cash, 0, true),
                    this.numberFormat(this.character.bank, 0, true)
                ),
                stocks: this.t("players.edit.stocks", this.numberFormat(this.character.stocksBalance, 0, true))
            };
        },
        getAvailableLicenses() {
            return ["heli", "fw", "cfi", "hw", "hwh", "perf", "management", "military", "utility", "commercial", "special", "hunting", "fishing", "weapon"].filter(l => !this.character.licenses.includes(l));
        },
        setPayCheck() {
            if (this.form.job_name === "Unemployed") {
                this.form.department_name = null;
                this.form.position_name = null;

                this.paycheck = 0;

                return;
            }

            for (let x = 0; x < jobsObject.length; x++) {
                const j = jobsObject[x];

                if (j.name === this.form.job_name) {
                    for (let y = 0; y < j.departments.length; y++) {
                        const d = j.departments[y];

                        if (d.name === this.form.department_name && this.form.position_name in d.positions) {
                            this.paycheck = d.positions[this.form.position_name];
                            return;
                        }
                    }
                }
            }

            this.paycheck = 0;
        },
        submit(isJobUpdate) {
            let form = this.form,
                query = '';
            if (isJobUpdate) {
                form.first_name = this.character.firstName;
                form.last_name = this.character.lastName;
                form.date_of_birth = this.character.dateOfBirth;
                form.gender = this.character.gender;
                form.backstory = this.character.backstory;
                query = '?jobUpdate=yes';
            } else {
                form.job_name = this.character.jobName;
                form.department_name = this.character.departmentName;
                form.position_name = this.character.positionName;
            }

            this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + query, form)
        },
        async deleteVehicle(e, vehicleId) {
            e.preventDefault();

            if (!confirm(this.t('players.vehicles.delete_vehicle'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/vehicles/delete/' + vehicleId);
        },
        sortJobs(array, type) {
            switch (type) {
                case 'job':
                case 'department':
                    array.sort((a, b) => {
                        return a.name.toLowerCase() < b.name.toLowerCase() ? -1 : 1
                    });
                    return array;
                case 'position':
                    array.sort((a, b) => {
                        return a.toLowerCase() < b.toLowerCase() ? -1 : 1
                    });
                    return array;
            }

            return [];
        },
        updateJob() {
            this.submit(true);
        },
        startEditVehicle(e, vehicle) {
            e.preventDefault();

            this.vehicleForm = vehicle;
            this.vehicleForm.repair = false;
            this.isVehicleEdit = true;
        },
        async removeTattoos() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/removeTattoos', {
                zone: $('#zone').val(),
            });

            // Reset.
            this.isTattooRemoval = false;
        },
        async resetSpawn() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/resetSpawn', {
                spawn: $('#spawn').val(),
            });

            // Reset.
            this.isResetSpawn = false;
        },
        async editVehicle() {
            if (this.isVehicleLoading) {
                return;
            }
            this.isVehicleLoading = true;

            this.vehicleForm.plate = this.vehicleForm.plate.toUpperCase().trim();

            try {
                const response = (await axios.post('/vehicles/edit/' + this.vehicleForm.id, this.vehicleForm)).data;

                if (response.status) {
                    $('.overflow-y-auto').scrollTop(0);
                    window.location.reload();
                } else {
                    this.vehicleEditError = response.message;

                    this.isVehicleLoading = false;
                    return;
                }
            } catch (e) {
            }

            this.isVehicleLoading = false;
            this.isVehicleEdit = false;
        },
        async addVehicle() {
            if (!this.vehicleMap.includes(this.vehicleAdd.value)) {
                alert('Unknown vehicle model "' + this.vehicleAdd.value + '"');
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/addVehicle', {
                model: this.vehicleAdd.value
            });

            // Reset.
            this.isVehicleAdd = false;
        },
        async addLicense() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/addLicense', {
                license: this.licenseForm.license
            });

            // Reset.
            this.isLicenceAdd = false;

            this.licenses = this.getAvailableLicenses();
        },
        async removeLicenses() {
            if (!confirm(this.t('players.characters.license.confirm'))) {
                this.isLicenceAdd = false;
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/addLicense', {
                license: 'remove'
            });

            // Reset.
            this.isLicenceAdd = false;

            this.licenses = this.getAvailableLicenses();
        },
        async editBalance() {
            // Send request.
            await this.$inertia.put('/players/' + this.player.steamIdentifier + '/characters/' + this.character.id + '/editBalance', this.balanceForm);

            const money = this.getMoneyLocals();

            this.local.cash = money.cash;
            this.local.cashTitle = money.cashTitle;
            this.local.stocks = money.stocks;

            this.balanceForm.cash = this.character.cash;
            this.balanceForm.bank = this.character.bank;
            this.balanceForm.stocks = this.character.stocksBalance;
        },
    },
    mounted() {
        this.setPayCheck();
    }
}
</script>
