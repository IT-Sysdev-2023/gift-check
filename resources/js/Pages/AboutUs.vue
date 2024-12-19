<template>
    <!-- back button -->

    <div @click="() => (backButton = true)">
        <button>
            <svg
                height="16"
                width="16"
                xmlns="http://www.w3.org/2000/svg"
                version="1.1"
                viewBox="0 0 1024 1024"
            >
                <path
                    d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"
                ></path>
            </svg>
            <span>EXIT</span>
        </button>
    </div>
    <a-modal v-model:open="backButton"> under construction </a-modal>

    <!-- welcome text  -->
    <div class="container">
        <div class="title">
            <h1>HELLO WORLD 👋</h1>
            <h2 style="text-align: center">Welcome to GIFT CHECK team</h2>
        </div>
        <div class="ghost">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- <div class="hello-world">
        <div class="animated-text">Hello World, Welcome to GIFT CHECK team</div>
    </div> -->
    <!-- dog -->

    <div class="main" style="margin-left: 55%; position: absolute; top: 10%">
        <div class="dog">
            <div class="dog__paws">
                <div class="dog__bl-leg leg">
                    <div class="dog__bl-paw paw"></div>
                    <div class="dog__bl-top top"></div>
                </div>
                <div class="dog__fl-leg leg">
                    <div class="dog__fl-paw paw"></div>
                    <div class="dog__fl-top top"></div>
                </div>
                <div class="dog__fr-leg leg">
                    <div class="dog__fr-paw paw"></div>
                    <div class="dog__fr-top top"></div>
                </div>
            </div>

            <div class="dog__body">
                <div class="dog__tail"></div>
            </div>

            <div class="dog__head">
                <div class="dog__snout">
                    <div class="dog__nose"></div>
                    <div class="dog__eyes">
                        <div class="dog__eye-l"></div>
                        <div class="dog__eye-r"></div>
                    </div>
                </div>
            </div>

            <div class="dog__head-c">
                <div class="dog__ear-l"></div>
                <div class="dog__ear-r"></div>
            </div>
        </div>
    </div>
    <div
        style="
            margin-top: 15%;
            display: flex;
            align-content: center;
            justify-content: center;
            font-weight: bold;
            font-size: large;
        "
    >
        SUPERVISED BY
    </div>

    <!-- supervised by card -->

    <div class="card" style="margin-left: 20%; position: absolute; top: 45%">
        <div>
            <img
                src="/images/maamFuertes.jpg"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Maria Neliza Uy Fuertes</p>
            <p class="text-body">Group Managing Director</p>
            <p style="color: rgb(134, 134, 134); position: absolute; top: 75%">
                CIA, CSCU, CISA, REB, REA, CICA, CrFA
            </p>
        </div>
        <button @click="fuertesModalOpen" class="card-button">More info</button>
    </div>
    <div style="margin-left: 58%; position: absolute; top: 45%" class="card">
        <div>
            <img
                src="/images/maamTina.jpg"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Maria Cristina C. Evarle</p>
            <p class="text-body">Jr. Supervisor</p>
        </div>
        <button @click="tinaModalOpen" class="card-button">More info</button>
    </div>

    <!-- programmers -->
    <div
        style="
            margin-top: 30%;
            display: flex;
            align-content: center;
            justify-content: center;
            font-weight: bold;
            font-size: large;
        "
    >
        GC PROGRAMMERS
    </div>

    <div style="margin-left: 70%; position: absolute; top: 125%" class="card">
        <div>
            <img
                src="/images/bossing.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Teofredo M. Gamale</p>
            <p class="text-body">GC Jr/Programmer</p>
        </div>
        <button class="card-button">More info</button>
    </div>

    <div style="margin-left: 50%; position: absolute; top: 125%" class="card">
        <div>
            <img
                src="/images/coco.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Harvey J. Barace</p>
            <p class="text-body">GC Sr/Programmer</p>
        </div>
        <button class="card-button">More info</button>
    </div>

    <div style="margin-left: 30%; position: absolute; top: 125%" class="card">
        <div>
            <img
                src="/images/jonny.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Jessan P. Palban</p>
            <p class="text-body">GC Sr/Programmer</p>
        </div>
        <button class="card-button">More info</button>
    </div>

    <div style="margin-left: 10%; position: absolute; top: 125%" class="card">
        <div>
            <img
                src="/images/manny.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Kent H. Abarques</p>
            <p class="text-body">GC Team Leader/Programmer</p>
        </div>
        <button @click="kentModalOpen" class="card-button">More info</button>
    </div>

    <!-- analyst -->

    <h5 id="animated">GC SYSTEM ANALYST</h5>

    <div style="margin-left: 20%; position: absolute; top: 200%" class="card">
        <div>
            <img
                src="/images/dog.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Claire Joy B. Cagas</p>
            <p class="text-body">GC System Analyst</p>
        </div>
        <button class="card-button">More info</button>
    </div>
    <div style="margin-left: 40%; position: absolute; top: 200%" class="card">
        <div>
            <img
                src="/images/cutedog.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Caren S. Norien</p>
            <p class="text-body">GC System Analyst I</p>
        </div>
        <button class="card-button">More info</button>
    </div>
    <div style="margin-left: 60%; position: absolute; top: 200%" class="card">
        <div>
            <img
                src="/images/scareddog.webp"
                style="
                    border-radius: 50%;
                    width: 180px;
                    height: 180px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            />
        </div>
        <div class="card-details">
            <p class="text-title">Maricel D. Dupit</p>
            <p class="text-body">GC System Analyst</p>
        </div>
        <button class="card-button">More info</button>
    </div>

    <!-- back to top button  -->

    <button v-if="showButton" @click="scrollToTop" class="button">
        <svg class="svgIcon" viewBox="0 0 384 512">
            <path
                d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"
            ></path>
        </svg>
    </button>
    <a-modal v-model:open="fuertesModal" :footer="false">
        <div
            class="w-60 bg-gradient-to-l from-slate-300 to-slate-100 text-slate-600 border border-slate-300 grid grid-col-2 justify-center p-4 gap-4 rounded-lg shadow-md"
        >
            <div class="col-span-2 text-lg font-bold capitalize rounded-md">
                card title
            </div>
            <div class="col-span-2 rounded-md">
                Using Lorem ipsum to focus attention on graphic elements in a
                webpage design proposal · One of the earliest examples of the
                Lorem ipsum placeholder text on 1960s advertising...
            </div>
            <div class="col-span-1">
                <button
                    class="rounded-md bg-slate-300 hover:bg-slate-600 hover:text-slate-200 duration-300 p-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-external-link"
                    >
                        <path
                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                        ></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </button>
            </div>
        </div>
    </a-modal>
    <a-modal v-model:open="tinaModal" :footer="false">
        <div
            class="w-60 bg-gradient-to-l from-slate-300 to-slate-100 text-slate-600 border border-slate-300 grid grid-col-2 justify-center p-4 gap-4 rounded-lg shadow-md"
        >
            <div class="col-span-2 text-lg font-bold capitalize rounded-md">
                card title
            </div>
            <div class="col-span-2 rounded-md">
                Using Lorem ipsum to focus attention on graphic elements in a
                webpage design proposal · One of the earliest examples of the
                Lorem ipsum placeholder text on 1960s advertising...
            </div>
            <div class="col-span-1">
                <button
                    class="rounded-md bg-slate-300 hover:bg-slate-600 hover:text-slate-200 duration-300 p-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-external-link"
                    >
                        <path
                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                        ></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </button>
            </div>
        </div>
    </a-modal>
    <a-modal v-model:open="kentModal" :footer="false">
        <div
            class="w-60 bg-gradient-to-l from-slate-300 to-slate-100 text-slate-600 border border-slate-300 grid grid-col-2 justify-center p-4 gap-4 rounded-lg shadow-md"
        >
            <div class="col-span-2 text-lg font-bold capitalize rounded-md">
                card title
            </div>
            <div class="col-span-2 rounded-md">
                Using Lorem ipsum to focus attention on graphic elements in a
                webpage design proposal · One of the earliest examples of the
                Lorem ipsum placeholder text on 1960s advertising...
            </div>
            <div class="col-span-1">
                <button
                    class="rounded-md bg-slate-300 hover:bg-slate-600 hover:text-slate-200 duration-300 p-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-external-link"
                    >
                        <path
                            d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"
                        ></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </button>
            </div>
        </div>
    </a-modal>
</template>

<script setup>
import { ref, onMounted, onUnmounted, registerRuntimeCompiler } from "vue";
import axios, { Axios } from "axios";
import StoreAccountingDashboard from "./StoreAccounting/StoreAccountingDashboard.vue";
import { notification } from "ant-design-vue";

const showButton = ref(false);
const backButton = ref(false);
const fuertesModal = ref(false);
const tinaModal = ref(false);
const kentModal = ref(false);

const fuertesModalOpen = () => {
    fuertesModal.value = true;
};

const tinaModalOpen = () => {
    tinaModal.value = true;
};

const kentModalOpen = () => {
    kentModal.value = true;
};

const handleScroll = () => {
    showButton.value = window.scrollY > 100;
};
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>
<style>
h5 {
  position: absolute;
  top: 50%;
  transform: translate(calc(50vw - 50%), -55%);
  font-size: 2em;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.wavy {
  animation: animate 7s ease-in-out infinite;
}

@keyframes animate {
  0%, 100% {
    clip-path: polygon(0 45%, 6% 38%, 20% 27%, 38% 24%, 40% 47%, 49% 64%, 51% 72%, 74% 78%, 79% 75%, 100% 67%, 100% 100%);
  }
  50% {
    clip-path: polygon(0 59%, 5% 71%, 24% 86%, 34% 71%, 41% 64%, 41% 46%, 51% 35%, 74% 21%, 89% 35%, 100% 42%, 100% 100%);
  }
}

/* Color gradient */
.wavy::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: linear-gradient(to bottom, #4a4a4a 0%, #10a8d7 50%, #4a4a4a 100%);
  background-size: 100% 200%;
  animation: color-wave 7s ease-in-out infinite;
}

@keyframes color-wave {
  0% {
    background-position: 0% 0%;
  }
  100% {
    background-position: 100% 0%;
  }
}
</style>

<style scoped>
/* Back button */
button {
    font-weight: bold;
    display: flex;
    height: 3em;
    width: 100px;
    align-items: center;
    justify-content: center;
    background-color: #eeeeee4b;
    border-radius: 3px;
    letter-spacing: 1px;
    transition: all 0.2s linear;
    cursor: pointer;
    border: none;
    background: #fff;
    position: absolute;
    top: 10px;
}

button > svg {
    margin-right: 5px;
    margin-left: 5px;
    font-size: 20px;
    transition: all 0.4s ease-in;
}

button:hover > svg {
    font-size: 1.2em;
    transform: translateX(-5px);
}

button:hover {
    box-shadow:
        9px 9px 33px #d1d1d1,
        -9px -9px 33px #ffffff;
    transform: translateY(-2px);
}
/*hello world*/
.hello-world {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    text-align: center;
}

.animated-text {
    position: absolute;
    top: 10%;
    font-size: 24px;
    font-weight: bold;
    color: blue;
    animation: fadeInMove 2s ease-in-out;
}

/* Define the animation */
@keyframes fadeInMove {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
/* supervised by card */
.card {
    width: 310px;
    height: 400px;
    border-radius: 20px;
    background: #f5f5f5;
    position: relative;
    padding: 1.8rem;
    border: 2px solid #c3c6ce;
    transition: 0.5s ease-out;
    overflow: visible;
}

.card-details {
    color: black;
    height: 100%;
    gap: 0.5em;
    display: grid;
    place-content: center;
}

.card-button {
    margin-top: 100%;
    transform: translate(-50%, 125%);
    width: 60%;
    border-radius: 1rem;
    border: none;
    background-color: #1e90ff;
    color: #fff;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    position: absolute;
    left: 50%;
    bottom: 0;
    opacity: 0;
    transition: 0.3s ease-out;
}

.text-body {
    position: absolute;
    text-align: center;
    top: 70%;
    color: rgb(134, 134, 134);
}

/*Text*/
.text-title {
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
    position: absolute;
    top: 60%;
}

/*Hover*/
.card:hover {
    border-color: #008bf8;
    box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.25);
}

.card:hover .card-button {
    transform: translate(-50%, 50%);
    opacity: 1;
}
/*dog*/
.main {
    position: relative;
    width: 23.5vmax;
    height: 23.5vmax;
    display: flex;
    justify-content: center;
    align-items: center;
}

.leg {
    position: absolute;
    bottom: 0;
    width: 2vmax;
    height: 2.125vmax;
}

.paw {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 1.95vmax;
    height: 1.875vmax;
    overflow: hidden;
}

.paw::before {
    content: "";
    position: absolute;
    width: 3.75vmax;
    height: 3.75vmax;
    border-radius: 50%;
}

.top {
    position: absolute;
    bottom: 0;
    left: 0.75vmax;
    height: 4.5vmax;
    width: 2.625vmax;
    border-top-left-radius: 1.425vmax;
    border-top-right-radius: 1.425vmax;
    transform-origin: bottom right;
    transform: rotateZ(90deg) translateX(-0.1vmax) translateY(1.5vmax);
    z-index: -1;
    background-image: linear-gradient(70deg, transparent 20%, #ff8b56 20%);
}

.dog {
    position: relative;
    width: 22.5vmax;
    height: 8.25vmax;
}

.dog::before {
    content: "";
    position: absolute;
    bottom: -0.75vmax;
    right: -0.15vmax;
    width: 100%;
    height: 1.5vmax;
    background-color: rgba(28, 49, 48, 0.1);
    border-radius: 50%;
    z-index: -1000;
    animation: shadow 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__head {
    position: absolute;
    left: 4.5vmax;
    bottom: 0;
    width: 9.75vmax;
    height: 8.25vmax;
    border-top-left-radius: 4.05vmax;
    border-top-right-radius: 4.05vmax;
    border-bottom-right-radius: 3.3vmax;
    border-bottom-left-radius: 3.3vmax;
    background-color: #ff8147;
    animation: head 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__head-c {
    position: absolute;
    left: 1.5vmax;
    bottom: 0;
    width: 9.75vmax;
    height: 8.25vmax;
    animation: head 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
    z-index: -1;
}

.dog__snout {
    position: absolute;
    left: -1.5vmax;
    bottom: 0;
    width: 7.5vmax;
    height: 3.75vmax;
    border-top-right-radius: 3vmax;
    border-bottom-right-radius: 3vmax;
    border-bottom-left-radius: 4.5vmax;
    background-color: #d7dbd2;
    animation: snout 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__snout::before {
    content: "";
    position: absolute;
    left: -0.1125vmax;
    top: -0.15vmax;
    width: 1.875vmax;
    height: 1.125vmax;
    border-top-right-radius: 3vmax;
    border-bottom-right-radius: 3vmax;
    border-bottom-left-radius: 4.5vmax;
    background-color: #1c3130;
    animation: snout-b 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__nose {
    position: absolute;
    top: -1.95vmax;
    left: 40%;
    width: 0.75vmax;
    height: 2.4vmax;
    border-radius: 0.525vmax;
    transform-origin: bottom;
    transform: rotateZ(10deg);
    background-color: #d7dbd2;
}

.dog__eye-l,
.dog__eye-r {
    position: absolute;
    top: -0.9vmax;
    width: 0.675vmax;
    height: 0.375vmax;
    border-radius: 50%;
    background-color: #1c3130;
    animation: eye 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__eye-l {
    left: 27%;
}

.dog__eye-r {
    left: 65%;
}

.dog__ear-l,
.dog__ear-r {
    position: absolute;
    width: 10.5vmax;
    height: 3.375vmax;
    border-top-left-radius: 0vmax;
    border-top-right-radius: 0vmax;
    border-bottom-right-radius: 3.3vmax;
    border-bottom-left-radius: 3.3vmax;
    background-color: #e26538;
}

.dog__ear-l {
    top: 1.5vmax;
    left: 6vmax;
    transform-origin: bottom left;
    transform: rotateZ(-50deg);
    z-index: -1;
    animation: ear-l 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__ear-r {
    top: 1.5vmax;
    right: 3vmax;
    transform-origin: bottom right;
    transform: rotateZ(20deg);
    z-index: -2;
    animation: ear-r 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__body {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    position: absolute;
    bottom: 0.3vmax;
    left: 3.75vmax;
    width: 18.75vmax;
    height: 7.2vmax;
    border-top-left-radius: 3vmax;
    border-top-right-radius: 6vmax;
    border-bottom-right-radius: 1.5vmax;
    border-bottom-left-radius: 6vmax;
    background-color: #ff702e;
    z-index: -2;
    animation: body 10s cubic-bezier(0.3, 0.41, 0.18, 1.01) infinite;
}

.dog__tail {
    position: absolute;
    right: -3vmax;
    height: 1.5vmax;
    width: 4.5vmax;
    background-color: #e96839;
    border-radius: 1.5vmax;
}

.dog__paws {
    position: absolute;
    bottom: 0;
    left: 7.5vmax;
    width: 12vmax;
    height: 3vmax;
}

.dog__bl-leg {
    left: -3vmax;
    z-index: -10;
}

.dog__bl-paw::before {
    background-color: #bec4b6;
}

.dog__bl-top {
    background-image: linear-gradient(80deg, transparent 20%, #e96839 20%);
}

.dog__fl-leg {
    z-index: 10;
    left: 0;
}

.dog__fl-paw::before {
    background-color: #d7dbd2;
}

.dog__fr-leg {
    right: 0;
}

.dog__fr-paw::before {
    background-color: #d7dbd2;
}

/*==============================*/

@keyframes head {
    0%,
    10%,
    20%,
    26%,
    28%,
    90%,
    100% {
        height: 8.25vmax;
        bottom: 0;
        transform-origin: bottom right;
        transform: rotateZ(0);
    }
    5%,
    15%,
    22%,
    24%,
    30% {
        height: 8.1vmax;
    }
    32%,
    50% {
        height: 8.25vmax;
    }
    55%,
    60% {
        bottom: 0.75vmax;
        transform-origin: bottom right;
        transform: rotateZ(0);
    }
    70%,
    80% {
        bottom: 0.75vmax;
        transform-origin: bottom right;
        transform: rotateZ(10deg);
    }
}

@keyframes body {
    0%,
    10%,
    20%,
    26%,
    28%,
    32%,
    100% {
        height: 7.2vmax;
    }
    5%,
    15%,
    22%,
    24%,
    30% {
        height: 7.05vmax;
    }
}

@keyframes ear-l {
    0%,
    10%,
    20%,
    26%,
    28%,
    82%,
    100% {
        transform: rotateZ(-50deg);
    }
    5%,
    15%,
    22%,
    24% {
        transform: rotateZ(-48deg);
    }
    30%,
    31% {
        transform: rotateZ(-30deg);
    }
    32%,
    80% {
        transform: rotateZ(-60deg);
    }
}

@keyframes ear-r {
    0%,
    10%,
    20%,
    26%,
    28% {
        transform: rotateZ(20deg);
    }
    5%,
    15%,
    22%,
    24% {
        transform: rotateZ(18deg);
    }
    30%,
    31% {
        transform: rotateZ(10deg);
    }
    32% {
        transform: rotateZ(25deg);
    }
}

@keyframes snout {
    0%,
    10%,
    20%,
    26%,
    28%,
    82%,
    100% {
        height: 3.75vmax;
    }
    5%,
    15%,
    22%,
    24% {
        height: 3.45vmax;
    }
}

@keyframes snout-b {
    0%,
    10%,
    20%,
    26%,
    28%,
    98%,
    100% {
        width: 1.875vmax;
    }
    5%,
    15%,
    22%,
    24% {
        width: 1.8vmax;
    }
    34%,
    98% {
        width: 1.275vmax;
    }
}

@keyframes shadow {
    0%,
    10%,
    20%,
    26%,
    28%,
    30%,
    84%,
    100% {
        width: 99%;
    }
    5%,
    15%,
    22%,
    24% {
        width: 101%;
    }
    34%,
    81% {
        width: 96%;
    }
}

@keyframes eye {
    0%,
    30% {
        width: 0.675vmax;
        height: 0.3vmax;
    }
    32%,
    59%,
    90%,
    100% {
        width: 0.525vmax;
        height: 0.525vmax;
        transform: translateY(0);
    }
    60%,
    75% {
        transform: translateY(-0.3vmax);
    }
    80%,
    85% {
        transform: translateY(0.15vmax);
    }
}
/* From Uiverse.io by vinodjangid07 */
.button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: rgb(20, 20, 20);
    border: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 0px 0px 4px rgba(180, 160, 255, 0.253);
    cursor: pointer;
    transition-duration: 0.3s;
    overflow: hidden;
    position: fixed;
    margin-top: 42%;
    bottom: 20px;
    right: 30px;
    z-index: 99;
}

.svgIcon {
    width: 12px;
    transition-duration: 0.3s;
}

.svgIcon path {
    fill: white;
}

.button:hover {
    width: 140px;
    border-radius: 50px;
    transition-duration: 0.3s;
    background-color: #1e90ff;
    align-items: center;
}

.button:hover .svgIcon {
    /* width: 20px; */
    transition-duration: 0.3s;
    transform: translateY(-200%);
}

.button::before {
    position: absolute;
    bottom: -20px;
    content: "Back to Top";
    color: white;
    /* transition-duration: .3s; */
    font-size: 0px;
}

.button:hover::before {
    font-size: 13px;
    opacity: 1;
    bottom: unset;
    /* transform: translateY(-30px); */
    transition-duration: 0.3s;
}
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap");

* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    display: grid;
    place-items: center;

    --bkg: #5eb3fd;
    --white: #e7e6e6;

    background-color: var(--white);
    background-image: url("https://www.transparenttextures.com/patterns/concrete-wall.png");
}

.container {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    overflow: hidden;
}

.title {
    color: #1e90ff;
    position: absolute;
    top: 14%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.title h1 {
    color: #e26436;
    font-size: 25px;
    font-weight: 900;
    text-align: center;
    font-family: "Montserrat", sans-serif;
}

.ghost {
    width: 8vmin;
    height: 12vmin;
    background-color: var(--white);
    background-image: url("https://www.transparenttextures.com/patterns/concrete-wall.png");
    background-image: radial-gradient(
            ellipse at 35% 40%,
            #000 8%,
            transparent 0%
        ),
        radial-gradient(ellipse at 65% 40%, #000 8%, transparent 0%),
        radial-gradient(ellipse at 50% 60%, #000 8%, transparent 0%);
    border-radius: 100% / 70% 70% 0% 0%;
    transform: translateX(100em) rotateZ(-90deg);
    position: relative;
    opacity: 0.9;
    mix-blend-mode: exclusion;
    animation: ghostMove 4s ease-out infinite;
}
@keyframes ghostMove {
    0% {
        transform: translateX(30em) rotateZ(-90deg);
    }
    100% {
        transform: translateX(-35em) rotateZ(-90deg);
    }
}
.ghost div {
    position: absolute;
    width: 20%;
    background-color: var(--white);
    background-image: url("https://www.transparenttextures.com/patterns/concrete-wall.png");
}
.ghost div:nth-of-type(1) {
    height: 7vmin;
    left: 0;
    bottom: -6vmin;
    border-radius: 100% / 0% 0% 50% 50%;
}
.ghost div:nth-of-type(2),
.ghost div:nth-of-type(4) {
    height: 4vmin;
    left: 20%;
    bottom: -3vmin;
    border-radius: 100% / 50% 50% 0% 0%;
    background-color: transparent;
}
.ghost div:nth-of-type(3) {
    height: 4vmin;
    left: 40%;
    bottom: -3.95vmin;
    border-radius: 100% / 0% 0% 60% 60%;
    background-color: var(--white);
}
.ghost div:nth-of-type(4) {
    left: 60%;
}
.ghost div:nth-of-type(5) {
    height: 3vmin;
    left: 80%;
    bottom: -2.9vmin;
    border-radius: 100% / 0% 0% 70% 70%;
    background-color: var(--white);
}
</style>
