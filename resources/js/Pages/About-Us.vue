<template>
    <div class="min-h-screen bg-gray-200">
        <!-- Navigation -->
        <div class="sticky top-0 z-50 flex justify-center py-6 bg-white shadow-md">
            <nav>
                <ul class="flex gap-10 flex items-center">
                    <img src="/images/logo.jpg" alt="GIFTCHECK LOGO" class="w-12 h-12 rounded-full" />
                    <a @click="scrollToSection('home', $event)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300"
                        href="#">Home</a>
                    <a @click="scrollToSection('supervised_by', $event)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300"
                        href="#">Supervised By</a>
                    <a @click="scrollToSection('programmers', $event)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300"
                        href="#">Programmers</a>
                    <a @click="scrollToSection('system_analysts', $event)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300"
                        href="#">System
                        Analyst</a>
                    <a @click="scrollToSection('about_gc', $event)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300" href="#">About
                        GC</a>
                    <Link :href="route(exitRoute)"
                        class="text-gray-800  hover:text-[#0047AB] font-semibold transition duration-300">Exit</Link>
                </ul>
            </nav>
        </div>

        <!-- Scroll up button -->
        <div v-if="showScrollButton">
            <a-button
                class="fixed bottom-16 right-8 text-gray-800 hover:text-[#0047AB] font-semibold transition duration-300"
                @click="scrollBack">
                <UpCircleOutlined class="text-5xl" />
            </a-button>
        </div>

        <!-- Welcome Section (unchanged) -->
        <div id="home" class="text-center">
            <h1 class="text-4xl font-bold text-[#0047AB] mt-5">Welcome to the GIFTCHECK Team</h1>
            <p class="mt-4 text-gray-600">"In all your ways acknowledge Him, and He will make straight your paths."
                – Proverbs 3:6</p>
            <div class="flex justify-center mt-8">
                <img class="rounded-lg shadow-lg"
                    src="https://user-images.githubusercontent.com/74038190/219923823-bf1ce878-c6b8-4faa-be07-93e6b1006521.gif"
                    alt="Team Illustration" />
            </div>
        </div>

        <!-- Supervised By Section -->
        <div id="supervised_by" class="mt-24">
            <h2 class="text-3xl font-bold text-center text-[#0047AB] mt-10">Supervised By</h2>
            <div class="flex justify-center gap-16 mt-20">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[300px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('fuertes, maria') || '/images/new.jpg'" alt="Group Managing Director" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('fuertes, maria') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('fuertes, maria') }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500 text-center">CPA, CIA, CSCU, CISA, REB, REA, CICA, CrFA , REC
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[300px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('evarle, maria') || '/images/new.jpg'" alt="Jr Supervisor" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('evarle, maria') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('evarle, maria') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Programmers Section -->
        <div id="programmers" class="mt-24">
            <h2 class="text-3xl font-bold text-center text-[#0047AB] mt-10">Programmers</h2>
            <div class="flex justify-center gap-8 mt-20">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[230px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('abarquez, kent') || '/images/new.jpg'" alt="Kent Jeremai H. Abarquez" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('abarquez, kent') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('abarquez, kent') }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[230px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('palban, jessan') || '/images/new.jpg'" alt="Jessan P. Palban" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('palban, jessan') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('palban, jessan') }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[230px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('barace, harvey') || '/images/new.jpg'" alt="Harver J. Barace" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('barace, harvey') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('barace, harvey') }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[230px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('gamale, teofredo') || '/images/new.jpg'" alt="Teofredo M. Gamale Jr." />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('gamale, teofredo') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('gamale, teofredo') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- System Analyst Section -->
        <div id="system_analysts" class="mt-24">
            <h2 class="text-3xl font-bold text-center text-[#0047AB] mt-10">System Analyst</h2>
            <div class="flex justify-center gap-8 mt-20">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[300px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('caren, norien') || '/images/new.jpg'" alt="Caren S. Norien" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('caren, norien') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('caren, norien') }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[300px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('cagas, claire') || '/images/new.jpg'" alt="Claire Joy B. Cagas" />
                    <h3 class="mt-4 text-xl font-semibold text-center">
                        {{ getEmployeeName('cagas, claire') }}
                    </h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('cagas, claire') }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img class="w-[300px] h-[300px] object-cover rounded-full mx-auto flex justify-center"
                        :src="getEmployeePhoto('dupit, maricel') || '/images/new.jpg'" alt="Maricel D. Dupit" />
                    <h3 class="mt-4 text-xl font-semibold text-center">Maricel D. Dupit</h3>
                    <p class="mt-2 text-gray-600 text-center">
                        {{ getEmployeePosition('dupit, maricel') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- About GC Section -->
        <div id="about_gc">
            <div>
                <h2 class="text-3xl font-bold text-center text-[#0047AB] mt-24">About GC</h2>
                <p class="mt-4 text-gray-800 text-center w-1/2 mx-auto"><span class="font-bold">Gift Check Monitoring
                        System</span> - is a system that
                    manages all
                    gift checks transactions. It includes Monitoring of Requests and Approval for Budget of GC
                    Production, Procurement and Receiving of Gift Checks, Store GC Allocation, Store GC Request, GC
                    Releasing and Budget Adjustments. It involves the following departments: Corporate Treasury,
                    Corporate Finance, Corporate Marketing, FAD, IAD and Stores.</p>
                <p class="text-center text-red-600 mt-5">For questions and concerns contact: Corp IT - 1953 and look
                    for
                    Norien or Claire.
                    Thank You.</p>
            </div>
        </div>
        <!-- Footer -->
        <footer class="py-6 text-gray-800 text-center bg-white shadow-md mt-10">
            <p>&copy; 2024 - {{ dayjs().year() }} <a href="//gift-check.com"
                    class="text-blue-800 hover:underline">gift-check.com</a>
                All rights reserved.</p>
            <div class="mt-5">
                <div class="parent">
                    <div class="child child-1">
                        <button class="button btn-1" @click="openTwitter">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="#1e90ff">
                                <path
                                    d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="child child-2">
                        <button class="button btn-2" @click="openInstagram">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="#ff00ff">
                                <path
                                    d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z">
                                </path>
                            </svg></button>
                    </div>
                    <div class="child child-3">
                        <button class="button btn-3" @click="openGithub">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 496 512">
                                <path
                                    d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z">
                                </path>
                            </svg></button>
                    </div>
                    <div class="child child-4">
                        <button class="button btn-4" @click="openFacebook">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512" fill="#4267B2">
                                <path
                                    d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z">
                                </path>
                            </svg></button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref, computed, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import { usePage } from '@inertiajs/vue3';

const page = usePage().props;
const exitRoute = computed(() => {
    const userType = {
        1: 'admin.dashboard',
        2: 'treasury.dashboard',
        3: 'finance.dashboard',
        4: 'custodian.dashboard',
        6: 'marketing.dashboard',
        7: 'retail.dashboard',
        8: 'retailgroup.dashboard',
        9: 'accounting.dashboard',
        10: 'iad.dashboard',
        12: 'eod.dashboard',
        13: 'storeaccounting.dashboard',
    };
    return userType[page.auth.user.usertype] ?? '';
})

const names = [
    'gamale, teofredo',
    'palban, jessan',
    'barace, harvey',
    'abarquez, kent',
    'cagas, claire',
    'caren, norien',
    'dupit, maricel',
    'fuertes, maria',
    'evarle, maria'
];
const employeeRecords = ref({});

// fetchData from HRMS Api
const fetchData = async () => {
    try {
        const requests = names.map(name =>
            axios.get('http://172.16.161.34/api/hrms/filter/employee/name', {
                params: { q: name }
            })
        );

        const responses = await Promise.all(requests);
        console.log(responses);

        responses.forEach((response, index) => {
            const name = names[index];
            if (response.data?.data?.employee?.[0]) {
                employeeRecords.value[name] = response.data.data.employee[0];
            }
        });

    } catch (error) {
        console.error(error);
    }
};
// employee photos
const getEmployeePhoto = (name) => {
    const record = employeeRecords.value[name];
    if (record && record.employee_photo) {
        return `http://172.16.161.34:8080/hrms/${record.employee_photo.replace(/^\.\.\//, '')}`;
    }
    return '/images/new.jpg';
};

// employee positions
const getEmployeePosition = (name) => {
    const record = employeeRecords.value[name];
    if (record && record.employee_position) {
        return record.employee_position;
    }
    return 'Inactive';
};

// employee names
const getEmployeeName = (name) => {
    const record = employeeRecords.value[name];
    if (record && record.employee_name) {
        return record.employee_name;
    }
    return '';
}

onMounted(() => {
    fetchData();
});

// scroll up function
const showScrollButton = ref(false);
const scrollBack = () => {
    window.scrollTo({
        top: 0, behavior: 'smooth'
    });
};
const showScrollBtn = () => {
    showScrollButton.value = window.scrollY > 600
};
onMounted(() => {
    window.addEventListener('scroll', showScrollBtn);
});
onUnmounted(() => {
    window.removeEventListener('scroll', showScrollBtn);
});

// navigation function
const scrollToSection = (section, event) => {
    event.preventDefault();
    const element = document.getElementById(section);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

const openTwitter = () => {
    window.open('https://twitter.com', '_blank');
}
const openFacebook = () => {
    window.open('https://facebook.com', '_blank');
}
const openInstagram = () => {
    window.open('https://instagram.com', '_blank');
}
const openGithub = () => {
    window.open('https://github.com/IT-Sysdev-2023/gift-check', '_blank')
}
</script>
<style scoped>
.parent {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.child {
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    transform-style: preserve-3d;
    transition: all 0.5s ease-in-out;
    border-radius: 50%;
    margin: 0 5px;
}

.child:hover {
    background-color: white;
    background-position: -100px 100px, -100px 100px;
    transform: rotate3d(0.5, 1, 0, 30deg);
    transform: perspective(180px) rotateX(60deg) translateY(2px);
    box-shadow: 0px 10px 10px rgb(1, 49, 182);
}

button {
    border: none;
    background-color: transparent;
    font-size: 20px;
}

.button:hover {
    width: inherit;
    height: inherit;
    display: flex;
    justify-content: center;
    align-items: center;
    transform: translate3d(0px, 0px, 15px) perspective(180px) rotateX(-35deg) translateY(2px);
    border-radius: 50%;
}
</style>
