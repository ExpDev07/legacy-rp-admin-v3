<template>
	<div>

		<portal to="title">
			<h1 class="dark:text-white">
				{{ t('logs.logs') }}
			</h1>
			<p>
				{{ t('logs.description') }}
			</p>
		</portal>

		<portal to="actions">
			<button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
					type="button" @click="refresh">
				<i class="mr-1 fa fa-refresh"></i>
				{{ t('logs.refresh') }}
			</button>
		</portal>

		<!-- Querying -->
		<v-section>
			<template #header>
				<h2>
					{{ t('logs.filter') }}
				</h2>
			</template>

			<template>
				<form @submit.prevent autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" class="hidden"/>

					<div class="flex flex-wrap mb-4">
						<!-- Identifier -->
						<div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
							<label class="block mb-2" for="identifier">
								{{ t('logs.identifier') }} <sup class="text-muted dark:text-dark-muted">*, C</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="identifier" placeholder="steam:11000010df22c8b" v-model="filters.identifier">
						</div>
						<!-- Action -->
						<div class="w-1/3 px-3 mobile:w-full mobile:mb-3 relative">
							<label class="block mb-2" for="action">
								{{ t('logs.action') }} <sup class="text-muted dark:text-dark-muted">**, C</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="action" :placeholder="t('logs.placeholder_action')" v-model="filters.action"
								   @keyup="searchActions()" @blur="cancelActionSearch()" @focus="searchActions()">
							<div class="w-full absolute top-full left-0 px-3 z-10"
								 v-if="searchingActions && searchableActions.length > 0">
								<div class="max-h-40 overflow-y-auto rounded-b border">
									<button
										class="block text-left w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 transition duration-200 hover:bg-gray-300"
										:class="{'border-b' : index < searchableActions.length-1}"
										v-for="(action, index) in searchableActions"
										@click="selectAction('=' + action.action)">
										{{ action.action }}
										<sup class="text-muted dark:text-dark-muted">{{
												numberFormat(action.count, 0, false)
											}}</sup>
									</button>
								</div>
							</div>
						</div>
						<!-- Server -->
						<div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
							<label class="block mb-2" for="server">
								{{ t('logs.server_id') }} <sup class="text-muted dark:text-dark-muted">*, C</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="server" placeholder="3" v-model="filters.server">
						</div>
						<!-- Details -->
						<div class="w-1/3 px-3">
							<label class="block mb-3 mt-3" for="details">
								{{ t('logs.details') }} <sup class="text-muted dark:text-dark-muted">**</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="details" :placeholder="t('logs.placeholder_details')" v-model="filters.details">
						</div>
						<!-- After Date -->
						<div class="w-1/6 px-3 pr-1 mobile:w-full mobile:mb-3">
							<label class="block mb-3 mt-3" for="after-date">
								{{ t('logs.after-date') }} <sup class="text-muted dark:text-dark-muted">*</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="after-date"
								   type="date" placeholder="">
						</div>
						<!-- After Time -->
						<div class="w-1/6 px-3 pl-1 mobile:w-full mobile:mb-3">
							<label class="block mb-3 mt-3" for="after-time">
								{{ t('logs.after-time') }} <sup class="text-muted dark:text-dark-muted">*</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="after-time" type="time" placeholder="">
						</div>
						<!-- Before Date -->
						<div class="w-1/6 px-3 pr-1 mobile:w-full mobile:mb-3">
							<label class="block mb-3 mt-3" for="before-date">
								{{ t('logs.before-date') }} <sup class="text-muted dark:text-dark-muted">*</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="before-date" type="date" placeholder="">
						</div>
						<!-- Before Time -->
						<div class="w-1/6 px-3 pl-1 mobile:w-full mobile:mb-3">
							<label class="block mb-3 mt-3" for="before-time">
								{{ t('logs.before-time') }} <sup class="text-muted dark:text-dark-muted">*</sup>
							</label>
							<input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
								   id="before-time" type="time" placeholder="">
						</div>
					</div>
					<!-- Description -->
					<div class="w-full px-3 mt-3">
						<small class="text-muted dark:text-dark-muted mt-1 leading-4 block">*
							{{ t('global.search.exact') }}</small>
						<small class="text-muted dark:text-dark-muted mt-1 leading-4 block">**
							{{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
						<small class="text-muted dark:text-dark-muted mt-1 leading-4 block">C
							{{ t('global.search.comma') }}</small>
					</div>
					<!-- Search button -->
					<div class="w-full px-3 mt-3">
						<button
							class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg"
							@click="refresh">
							<span v-if="!isLoading">
								<i class="fas fa-search"></i>
								{{ t('logs.search') }}
							</span>
							<span v-else>
								<i class="fas fa-cog animate-spin"></i>
								{{ t('global.loading') }}
							</span>
						</button>

						<button
							class="px-5 py-2 ml-5 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg"
							@click="showDrugLogs"
							v-if="canSearchDrugs">
							{{ t('logs.drug_search') }}
						</button>
					</div>
				</form>
			</template>
		</v-section>

		<!-- Table -->
		<v-section class="overflow-x-auto">
			<template #header>
				<h2>
					{{ t('logs.logs') }}
				</h2>
				<p class="text-muted dark:text-dark-muted text-xs">
					{{ t('global.results', time) }}
				</p>
			</template>

			<template>
				<table class="w-full whitespace-no-wrap">
					<tr class="font-semibold text-left mobile:hidden">
						<th class="px-6 py-4">{{ t('logs.player') }}</th>
						<th class="px-6 py-4">{{ t('logs.server_id') }}</th>
						<th class="px-6 py-4">{{ t('logs.action') }}</th>
						<th class="px-6 py-4">{{ t('logs.details') }}</th>
						<th class="px-6 py-4">
							{{ t('logs.timestamp') }}
							<a href="#" :title="t('logs.toggle_diff')"
							   @click="$event.preventDefault();showLogTimeDifference = !showLogTimeDifference">
								<i class="fas fa-stopwatch"></i>
							</a>
						</th>
					</tr>
					<tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="(log, index) in logs"
						:key="log.id">
						<td class="px-6 py-3 border-t mobile:block">
							<inertia-link
								class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400"
								:href="'/players/' + log.steamIdentifier">
								{{ playerName(log.steamIdentifier) }}
							</inertia-link>
						</td>
						<td class="px-6 py-3 border-t mobile:block" :title="t('global.server_timeout')">
							<a class="font-semibold" :href="'/map#server_' + log.status.serverId"
							   :title="t('global.view_map')" v-if="log.status.status === 'online'">
								{{ log.status.serverId }}
							</a>
							<span class="font-semibold" v-else>
								{{ t('global.status.' + log.status.status) }}
							</span>
						</td>
						<td class="px-6 py-3 border-t mobile:block">
							{{ log.action }}
							<a href="#" @click="detailedAction($event, log)"
							   class="block text-xs leading-1 text-blue-600 dark:text-blue-400 whitespace-nowrap"
							   v-if="log.metadata">
								{{ t('logs.metadata.show') }}
							</a>
						</td>
						<td class="px-6 py-3 border-t mobile:block" v-html="parseLog(log.details, log.action, log.metadata)"></td>
						<td class="px-6 py-3 border-t mobile:block" v-if="showLogTimeDifference"
							:title="t('logs.diff_label')">
							<span v-if="index+1 < logs.length">
								{{ formatSecondDiff(stamp(log.timestamp) - stamp(logs[index + 1].timestamp)) }}
								<i class="fas fa-arrow-down"></i>
							</span>
							<span v-else>Start</span>
						</td>
						<td class="px-6 py-3 border-t mobile:block" v-else>
							{{ log.timestamp | formatTime(true) }}
							<i class="block text-xs leading-1 whitespace-nowrap text-yellow-600 dark:text-yellow-400">{{
									formatRawTimestamp(log.timestamp)
								}}</i>
						</td>
					</tr>
					<tr v-if="logs.length === 0">
						<td class="px-4 py-6 text-center border-t" colspan="100%">
							{{ t('logs.no_logs') }}
						</td>
					</tr>
				</table>
			</template>

			<template #footer>
				<div class="flex items-center justify-between mt-6 mb-1">

					<!-- Navigation -->
					<div class="flex flex-wrap">
						<inertia-link
							class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
							:href="links.prev"
							v-if="page >= 2"
						>
							<i class="mr-1 fas fa-arrow-left"></i>
							{{ t("pagination.previous") }}
						</inertia-link>
						<inertia-link
							class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
							v-if="logs.length === 15"
							:href="links.next"
						>
							{{ t("pagination.next") }}
							<i class="ml-1 fas fa-arrow-right"></i>
						</inertia-link>
					</div>

					<!-- Meta -->
					<div class="font-semibold">
						{{ t("pagination.page", page) }}
					</div>

				</div>
			</template>
		</v-section>

		<modal :show.sync="showLogDetail">
			<template #header>
				<h1 class="dark:text-white">
					{{ t('logs.detail.title') }}
				</h1>
				<p class="dark:text-dark-muted !-mt-3 italic">
					{{ t('logs.detail.description', log_detail.user) }}
				</p>
			</template>

			<template #default>
				<pre class="text-lg block mb-2 whitespace-pre-wrap">{{ log_detail.reason }}</pre>
				{{ log_detail.description }}
			</template>

			<template #actions>
				<button type="button"
						class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400"
						@click="showLogDetail = false">
					{{ t('global.close') }}
				</button>
			</template>
		</modal>

		<modal :show.sync="showLogMetadata">
			<template #header>
				<h1 class="dark:text-white">
					{{ t('logs.metadata.title') }}
				</h1>
			</template>

			<template #default>
				<p class="m-0 mb-2 font-bold">{{ t('logs.metadata.details') }}:</p>
				<pre class="block text-sm whitespace-pre break-words border-dashed border-b-2 mb-4 pb-4">{{
						parseLogMetadata(logMetadata) || 'N/A'
					}}</pre>

				<pre class="block text-sm whitespace-pre break-words border-dashed border-b-2 mb-4 pb-4" v-if="parsedMetadata"
					v-html="parsedMetadata"></pre>

				<p class="m-0 mb-2 font-bold">{{ t('logs.metadata.raw') }}:</p>
				<pre class="block text-xs whitespace-pre break-words hljs px-3 py-2 rounded"
					v-html="logMetadataJSON"></pre>

				<p class="m-0 mt-2 mb-2 font-bold" v-if="metaScreenshot">{{ t('logs.metadata.screenshot') }}:</p>
				<img :src="metaScreenshot" class="w-full" v-if="metaScreenshot" />
			</template>

			<template #actions>
				<button type="button"
						class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400"
						@click="showLogMetadata = false; logMetadata = null">
					{{ t('global.close') }}
				</button>
			</template>
		</modal>

	</div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from './../../Components/Pagination';
import Modal from './../../Components/Modal';

import hljs from 'highlight.js';

import json from 'highlight.js/lib/languages/json';

hljs.registerLanguage('json', json);

import 'highlight.js/styles/github-dark-dimmed.css';

export default {
	layout: Layout,
	components: {
		Pagination,
		Modal,
		VSection
	},
	props: {
		logs: {
			type: Array,
			required: true,
		},
		drugActions: {
			type: Array,
			required: true,
		},
		filters: {
			identifier: String,
			action: String,
			server: String,
			details: String,
			before: Number,
			after: Number,
		},
		playerMap: {
			type: Object,
			required: true,
		},
		links: {
			type: Object,
			required: true,
		},
		page: {
			type: Number,
			required: true,
		},
		time: {
			type: Number,
			required: true,
		},
		canSearchDrugs: {
			type: Boolean,
			required: true,
		},
		actions: {
			type: Array
		}
	},
	data() {
		return {
			isLoading: false,
			showLogDetail: false,
			log_detail: {
				user: '',
				reason: '',
				description: ''
			},
			showLogTimeDifference: false,
			logMetadata: null,
			showLogMetadata: false,
			metaScreenshot: null,
			logMetadataJSON: '',
			parsedMetadata: '',
			searchingActions: false,
			searchableActions: [],

			searchTimeout: false
		};
	},
	methods: {
		selectAction(action) {
			clearTimeout(this.searchTimeout);

			this.filters.action = action;
			this.searchingActions = false;
		},
		cancelActionSearch() {
			clearTimeout(this.searchTimeout);

			this.searchTimeout = setTimeout(() => {
				this.searchingActions = false;
			}, 250);
		},
		searchActions() {
			clearTimeout(this.searchTimeout);

			let search = this.filters.action ? this.filters.action.trim().toLowerCase() : '';

			search = search.startsWith('=') ? search.substring(1) : search;

			if (search === '') {
				this.searchingActions = false;

				return;
			}

			const actions = this.actions ? this.actions.filter(action => action.action.toLowerCase().includes(search)) : [];
			actions.sort((a, b) => {
				return b.count - a.count;
			});

			this.searchableActions = actions;
			this.searchingActions = true;
		},
		showDrugLogs() {
			this.filters.action = this.drugActions.map(e => '=' + e).join(',');

			this.refresh();
		},
		formatRawTimestamp(timestamp) {
			return this.$moment(timestamp).unix();
		},
		formatSecondDiff(sec) {
			return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
		},
		stamp(time) {
			return this.$moment.utc(time).unix();
		},
		detailedAction(e, log) {
			e.preventDefault();

			const metadata = log.metadata;

			if (metadata) {
				this.logMetadata = metadata;
				this.logMetadataJSON = hljs.highlight(JSON.stringify(metadata, null, 4), {language: 'json'}).value;
				this.showLogMetadata = true;

				if (metadata.changes) {
					this.parsedMetadata = metadata.changes;
				} else {
					this.parsedMetadata = '';
				}

				this.metaScreenshot = metadata && metadata.screenshotURL ? metadata.screenshotURL : null;
			}
		},
		parseLogMetadata(metadata) {
			if (metadata && metadata.secondaryCause) {
				const source = metadata.secondaryCause.source ? metadata.secondaryCause.source : '/';

				switch (metadata.secondaryCause.label) {
					case 'Unknown':
						return this.t('logs.metadata.secondary_unknown');
					case 'Player':
						return this.t('logs.metadata.secondary_player', source);
					case 'NPC':
						return this.t('logs.metadata.secondary_npc');
					case 'Vehicle':
						return this.t('logs.metadata.secondary_vehicle', source);
					case 'Touching Vehicle':
						const vehicles = metadata.secondaryCause.source ? Object.entries(metadata.secondaryCause.source).map(e => e[0] + " [" + (e[1] ? e[1] : '/') + "]").join(', ') : 'N/A';

						return this.t('logs.metadata.secondary_touching', vehicles);
				}
			}

			return null;
		},
		async refresh() {
			if (this.isLoading) {
				return;
			}

			this.isLoading = true;
			try {
				const beforeDate = $('#before-date').val(),
					beforeTime = $('#before-time').val(),
					afterDate = $('#after-date').val(),
					afterTime = $('#after-time').val();

				if (beforeDate && beforeTime) {
					this.filters.before = Math.round((new Date(beforeDate + ' ' + beforeTime)).getTime() / 1000);

					if (isNaN(this.filters.before)) {
						this.filters.before = null;
					}
				}

				if (afterDate && afterTime) {
					this.filters.after = Math.round((new Date(afterDate + ' ' + afterTime)).getTime() / 1000);

					if (isNaN(this.filters.after)) {
						this.filters.after = null;
					}
				}

				await this.$inertia.replace('/logs', {
					data: this.filters,
					preserveState: true,
					preserveScroll: true,
					only: ['logs', 'playerMap', 'time', 'links', 'page'],
				});
			} catch (e) {
			}

			this.isLoading = false;
		},
		parseOtherLog(details, action, metadata) {
			const regex = /attempted to add a song with video ID `(.+?)` to boombox/gmi;
			const matches = details.matchAll(regex).next();
			const match = matches && matches.value ? matches.value[1] : null;

			if (match) {
				const html = `<a href="https://youtube.com/watch?v=${match}" target="_blank" class="text-blue-600 dark:text-blue-400">${match}</a>`;

				return details.replace(match, html);
			}

			return details;
		},
		parseDisconnectLog(details, action, metadata) {
			const regex = /(?<=\) has disconnected from the server .+? with reason: `)(.+?)(?=`\.)/gm;
			const matches = details.match(regex);
			const match = matches && matches.length === 1 && matches[0].trim() ? matches[0].trim() : null;

			if (match) {
				const descriptions = [
					[/^Exiting/gmi, this.t('logs.detail.reasons.exited')],
					[/^Disconnected|^You have disconnected from the server/gmi, this.t('logs.detail.reasons.disconnected')],
					[/Game crashed: /gmi, this.t('logs.detail.reasons.crash')],
					[/(?<=connection|You) timed out[!.]|^Timed out after/gmi, this.t('logs.detail.reasons.timeout')],
					[/^You have been banned/gmi, this.t('logs.detail.reasons.banned')],
					[/^The server is restarting/gmi, this.t('logs.detail.reasons.restart')],
					[/^You have been kicked/gmi, this.t('logs.detail.reasons.kicked')],
					[/^Your Job Priority expired/gmi, this.t('logs.detail.reasons.job')],
					[/^Failed to sync doors/gmi, this.t('logs.detail.reasons.doors')],
					[/^You have been globally banned from all OP-FW servers/gmi, this.t('logs.detail.reasons.global')],
					[/^Entering Rockstar Editor/gmi, this.t('logs.detail.reasons.editor')],
					[/^Reliable network event overflow/gmi, this.t('logs.detail.reasons.overflow')],
					[/^Connecting to another server/gmi, this.t('logs.detail.reasons.another')],
					[/^Obtaining configuration from server failed/gmi, this.t('logs.detail.reasons.config')],
				];

				let description = '';
				for (let x in descriptions) {
					const entry = descriptions[x];

					if (entry[0].test(match)) {
						description = entry[1];
						break;
					}
				}

				if (!description) {
					description = this.t('logs.detail.reasons.unknown');
				}

				const html = $('<div />').append(
					$('<a></a>', {
						"data-reason": match,
						"data-description": description,
						"class": "text-yellow-800 dark:text-yellow-200 exit-log",
						"href": "#"
					}).text(match)
				).html();

				return details.replace(match, html);
			}

			return this.parseOtherLog(details, action, metadata);
		},
		escapeHtml(unsafe) {
			return unsafe
				.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		},
		parseLog(details, action, metadata) {
			const regex = /(to|from) (inventory )?((trunk|glovebox|character|property|motel-\w+?|evidence|ground|locker-\w+?)-(\d+-)?\d+:\d+)/gmi;

			let inventories = [];

			details = this.escapeHtml(details);

			let m;
			while ((m = regex.exec(details)) !== null) {
				if (m.index === regex.lastIndex) {
					regex.lastIndex++;
				}

				if (m.length > 3 && m[3] && !inventories.includes(m[3])) {
					inventories.push(m[3]);
				}
			}

			for (let x = 0; x < inventories.length; x++) {
				details = details.replaceAll(inventories[x], '<a title="' + this.t('inventories.view') + '" class="text-indigo-600 dark:text-indigo-400" href="/inventory/' + inventories[x] + '">' + inventories[x] + '</a>');
			}

			if (metadata && metadata.killerSteam) {
				const killerSteam = metadata.killerSteam;

				details = details.replace(/killed by (.+?), death cause/gm, (match, playerName) => {
					return 'killed by <a class="text-red-600 dark:text-red-400" href="/players/' + killerSteam + '">' + playerName + '</a>, death cause';
				});
			}

			return this.parseDisconnectLog(details, action, metadata);
		},
		playerName(steamIdentifier) {
			return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
		}
	},
	mounted() {
		const _this = this;
		$('body').on('click', 'a.exit-log', function (e) {
			e.preventDefault();
			const parent = $(this).closest('tr');

			_this.showLogDetail = true;
			_this.log_detail.user = $('td:first-child a', parent).text().trim();
			_this.log_detail.reason = $(this).data('reason');
			_this.log_detail.description = $(this).data('description');
		});

		if (this.filters.before) {
			const d = new Date(this.filters.before * 1000);

			$('#before-date').val(d.getFullYear() + '-' + ((d.getMonth() + 1) + '').padStart(2, '0') + '-' + (d.getDate() + '').padStart(2, '0'));
			$('#before-time').val(d.getHours() + ':' + d.getMinutes());
		}
		if (this.filters.after) {
			const d = new Date(this.filters.after * 1000);

			$('#after-date').val(d.getFullYear() + '-' + ((d.getMonth() + 1) + '').padStart(2, '0') + '-' + (d.getDate() + '').padStart(2, '0'));
			$('#after-time').val(d.getHours() + ':' + d.getMinutes());
		}
	}
};
</script>
