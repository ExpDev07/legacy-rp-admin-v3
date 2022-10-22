<template>
    <svg v-if="name === 'apple'" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
        <g fill-rule="nonzero">
            <path d="M46.173 19.967C49.927-1.838 19.797-.233 14.538.21c-.429.035-.648.4-.483.8 2.004 4.825 14.168 31.66 32.118 18.957zm13.18 1.636c1.269-.891 1.35-1.614.047-2.453l-2.657-1.71c-.94-.607-1.685-.606-2.532.129-5.094 4.42-7.336 9.18-8.211 15.24 1.597.682 3.55.79 5.265.328 1.298-4.283 3.64-8.412 8.088-11.534z" />
            <path d="M88.588 67.75c9.65-27.532-13.697-45.537-35.453-32.322-1.84 1.118-4.601 1.118-6.441 0-21.757-13.215-45.105 4.79-35.454 32.321 5.302 15.123 17.06 39.95 37.295 29.995.772-.38 1.986-.38 2.758 0 20.235 9.955 31.991-14.872 37.295-29.995z" />
        </g>
    </svg>
    <svg v-else-if="name === 'book'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M6 4H5a1 1 0 1 1 0-2h11V1a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V5a1 1 0 0 0-1-1h-7v8l-2-2-2 2V4z" />
    </svg>
    <svg v-else-if="name === 'cheveron-down'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
    </svg>
    <svg v-else-if="name === 'cheveron-right'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <polygon points="12.95 10.707 13.657 10 8 4.343 6.586 5.757 10.828 10 6.586 14.243 8 15.657 12.95 10.707" />
    </svg>
    <svg v-else-if="name === 'dashboard'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm-5.6-4.29a9.95 9.95 0 0 1 11.2 0 8 8 0 1 0-11.2 0zm6.12-7.64l3.02-3.02 1.41 1.41-3.02 3.02a2 2 0 1 1-1.41-1.41z" />
    </svg>
    <svg v-else-if="name === 'location'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
    </svg>
    <svg v-else-if="name === 'office'" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
        <path fill-rule="evenodd" d="M7 0h86v100H57.108V88.418H42.892V100H7V0zm9 64h11v15H16V64zm57 0h11v15H73V64zm-19 0h11v15H54V64zm-19 0h11v15H35V64zM16 37h11v15H16V37zm57 0h11v15H73V37zm-19 0h11v15H54V37zm-19 0h11v15H35V37zM16 11h11v15H16V11zm57 0h11v15H73V11zm-19 0h11v15H54V11zm-19 0h11v15H35V11z" />
    </svg>
    <svg v-else-if="name === 'printer'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M4 16H0V6h20v10h-4v4H4v-4zm2-4v6h8v-6H6zM4 0h12v5H4V0zM2 8v2h2V8H2zm4 0v2h2V8H6z" />
    </svg>
    <svg v-else-if="name === 'shopping-cart'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M4 2h16l-3 9H4a1 1 0 1 0 0 2h13v2H4a3 3 0 0 1 0-6h.33L3 5 2 2H0V0h3a1 1 0 0 1 1 1v1zm1 18a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm10 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" />
    </svg>
    <svg v-else-if="name === 'store-front'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M18 9.87V20H2V9.87a4.25 4.25 0 0 0 3-.38V14h10V9.5a4.26 4.26 0 0 0 3 .37zM3 0h4l-.67 6.03A3.43 3.43 0 0 1 3 9C1.34 9 .42 7.73.95 6.15L3 0zm5 0h4l.7 6.3c.17 1.5-.91 2.7-2.42 2.7h-.56A2.38 2.38 0 0 1 7.3 6.3L8 0zm5 0h4l2.05 6.15C19.58 7.73 18.65 9 17 9a3.42 3.42 0 0 1-3.33-2.97L13 0z" />
    </svg>
    <svg v-else-if="name === 'trash'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z" />
    </svg>
    <svg v-else-if="name === 'user'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z" />
    </svg>
    <svg v-else-if="name === 'twitter'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <g xmlns="http://www.w3.org/2000/svg" transform="matrix(0.0671143,0,0,0.0671143,-36.2538,-36.2456)">
            <g id="layer1">
                <path id="path3611" d="M633.898,812.045C746.359,812.045 807.855,718.877 807.855,638.089C807.855,635.442 807.801,632.808 807.682,630.185C819.62,621.555 829.996,610.786 838.18,598.526C827.225,603.395 815.436,606.673 803.069,608.151C815.692,600.582 825.383,588.608 829.955,574.334C818.142,581.337 805.059,586.427 791.131,589.174C779.974,577.29 764.09,569.858 746.502,569.858C712.738,569.858 685.358,597.238 685.358,630.99C685.358,635.788 685.894,640.455 686.943,644.931C636.128,642.374 591.069,618.045 560.918,581.051C555.667,590.086 552.639,600.582 552.639,611.781C552.639,632.993 563.433,651.719 579.847,662.674C569.816,662.364 560.393,659.61 552.156,655.027C552.147,655.284 552.147,655.534 552.147,655.808C552.147,685.417 573.222,710.14 601.199,715.742C596.061,717.143 590.655,717.894 585.077,717.894C581.143,717.894 577.311,717.507 573.586,716.791C581.369,741.084 603.94,758.762 630.701,759.257C609.775,775.659 583.414,785.427 554.764,785.427C549.835,785.427 544.965,785.147 540.179,784.581C567.238,801.925 599.369,812.045 633.901,812.045" style="fill:rgb(255,255,255);fill-rule:nonzero;"/>
            </g>
        </g>
    </svg>
    <svg v-else-if="name === 'poker'" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 512 512" style="width:20px;height:20px;position:relative;left:-3px">
        <path d="m367 448 10-21h-20zM145 64l-10 21h20z"/>
        <path d="M418 0H94C80 0 68 11 68 26v460c0 15 12 26 26 26h324c14 0 26-11 26-26V26c0-15-12-26-26-26zM205 34h196c5 0 9 4 9 9v290a9 9 0 1 1-17 0V51H205a9 9 0 1 1 0-17zm11 108c14-12 34-17 53-13a63 63 0 0 1 45 87c10 1 20 4 30 10 19 12 30 30 32 49a66 66 0 0 1-65 67c-7 0-13-1-19-3l5 14 6 12c2 4 2 9 0 13-3 4-7 6-11 6h-72c-4 0-8-2-11-6-2-4-2-9 0-13l6-12 5-14a66 66 0 0 1-67-18 61 61 0 0 1-16-46c1-19 12-37 31-49 10-6 20-9 30-10a63 63 0 0 1 18-74zm-113-26 34-77a9 9 0 0 1 16 0l34 77a9 9 0 0 1-16 7l-9-21h-34l-9 21a9 9 0 0 1-16-7zm204 362H111c-5 0-9-4-9-9V179a9 9 0 1 1 17 0v282h188a9 9 0 1 1 0 17zm102-82-34 77a9 9 0 0 1-16 0l-34-77a9 9 0 1 1 16-7l9 21h34l9-21a9 9 0 0 1 16 7z"/>
    </svg>
    <i class="fas fa-clipboard-list text-lg" v-else-if="name === 'paperstack'"></i>
    <i class="fas fa-boxes" v-else-if="name === 'boxes'"></i>
    <i class="fas fa-users" v-else-if="name === 'users'"></i>
    <i class="fas fa-cogs" v-else-if="name === 'cogs'"></i>
    <i class="fab fa-searchengin" v-else-if="name === 'search'"></i>
    <i class="fas fa-heartbeat" v-else-if="name === 'heart'"></i>
    <i class="fas fa-map" v-else-if="name === 'map'"></i>
    <i class="fas fa-server" v-else-if="name === 'server'"></i>
    <i class="fas fa-chart-line" v-else-if="name === 'statistics'"></i>
    <i class="fas fa-pallet" v-else-if="name === 'pallet'"></i>
    <i class="fas fa-fingerprint" v-else-if="name === 'fingerprint'"></i>
    <i class="fas fa-shield-alt" v-else-if="name === 'shield'"></i>
    <i class="fas fa-shoe-prints" v-else-if="name === 'prints'"></i>
    <i class="fas fa-camera" v-else-if="name === 'camera'"></i>
    <i class="fas fa-bug" v-else-if="name === 'bug'"></i>
    <i class="fas fa-spider" v-else-if="name === 'spider'"></i>
    <i class="fas fa-viruses" v-else-if="name === 'virus'"></i>
    <i class="fas fa-images" v-else-if="name === 'images'"></i>
    <i class="fas fa-subway" v-else-if="name === 'subway'"></i>
    <i class="fas fa-kiwi-bird" v-else-if="name === 'kiwi'"></i>
    <i class="fas fa-user-slash" v-else-if="name === 'user-slash'"></i>
    <i class="fas fa-user-friends" v-else-if="name === 'friends'"></i>
    <i class="fas fa-user" v-else-if="name === 'user'"></i>
    <i class="fas fa-random" v-else-if="name === 'random'"></i>
    <i class="fas fa-box-open" v-else-if="name === 'box-open'"></i>
    <i class="fas fa-binoculars" v-else-if="name === 'binoculars'"></i>
    <i class="fas fa-ghost" v-else-if="name === 'ghost'"></i>
    <i class="fas fa-mobile-alt" v-else-if="name === 'phone'"></i>
    <i class="fas fa-spinner" v-else-if="name === 'spinner'"></i>
</template>

<script>
export default {
    props: {
        name: {
            type: String,
            required: true,
        },
    },
}
</script>
