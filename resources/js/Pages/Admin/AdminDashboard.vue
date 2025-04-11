<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="16">
                <a-row :gutter="[16, 16]">
                    <a-col :span="8">
                        <a-button @click="() => $inertia.get(route('admin.masterfile.users'))" class="buttons">
                            <UserOutlined /> Setup Users
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="buttons">
                            <AppstoreAddOutlined /> Setup Store Staff
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="buttons">
                            <CustomerServiceOutlined /> Setup Customer
                        </a-button>
                        <a-button @click="() => $inertia.get(route('admin.masterfile.designs'))" class="buttons">
                            <CustomerServiceOutlined /> Special Gc Design
                        </a-button>
                    </a-col>
                    <a-col :span="8">

                        <a-button @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))"
                            class="buttons">
                            <CreditCardFilled /> Setup Credit Card
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))"
                            class="buttons">
                            <BarcodeOutlined /> Setup Denomination
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="buttons">
                            <FundFilled /> Revolving Fund
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.store.verification'))" class="buttons">
                            <VerifiedOutlined /> Store Verification
                        </a-button>


                    </a-col>
                    <a-col :span="8">
                        <a-button @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="buttons">
                            <AppstoreFilled /> Setup Store
                        </a-button>
                        <a-button @click="() => $inertia.get(route('admin.masterfile.tagHennan'))" class="buttons">
                            <TagOutlined /> Tag Hennan
                        </a-button>

                        <a-button @click="() => $inertia.get(route('admin.masterfile.blockBarcode'))" class="buttons">
                            <StopOutlined /> Blocked Barcode
                        </a-button>
                    </a-col>
                </a-row>

            </a-col>
            <a-col :span="8">
                <a-card size="small" :title="'Online Users - ' + getOnlineUsers?.length">
                    <div class="scrollable-list">
                        <a-list size="small" item-layout="horizontal" :data-source="getOnlineUsers">
                            <template #renderItem="{ item }">
                                <a-list-item>
                                    <a-list-item-meta>
                                        <template #avatar>
                                            <div class="relative inline-block">
                                                <a-avatar :src="'http://172.16.161.34:8080/hrms' + item.image" />
                                                <span
                                                    class="absolute top-0 right-7 block h-3 w-3 bg-green-500 border-2 border-white rounded-full"></span>
                                            </div>
                                        </template>
                                        <template #title>
                                            {{ item.name }} - <a-tag color="#108ee9">{{ item.usertype }}</a-tag>
                                        </template>
                                        <template #description>
                                            <a-tag color="blue">{{ item.storeAssigned }}</a-tag>
                                        </template>
                                    </a-list-item-meta>
                                </a-list-item>
                            </template>
                        </a-list>
                    </div>
                </a-card>
            </a-col>
        </a-row>
        <a-card>
            <!-- Stores and Sales section  -->
            <div class="flex justify-center gap-5 mt-5">
                <div class="text-center font-bold" v-for="item in props.storeQuery" :key="item.store_name">
                    {{ item.store_name }}<br>
                    <p class="text-gray-500 mt-5 flex justify-center">Sales</p>
                    <p class="text-[#0047AB] text-2xl">
                        {{ item.gc_count }}
                    </p>
                </div>
            </div>
            <!--Graph section-->
            <section class="mt-10">
                <p class="text-lg text-center text-gray-700">Sales</p>
                <div class="chart-container">
                    <LineChart :chartData="chartData" :chartOptions="chartOptions" />
                </div>
            </section>

        </a-card>
        <!-- {{ regularGcData }} -->
        <!-- {{ storeQuery }} -->
        <!-- {{ getOnlineUsers }} -->

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LineChart from '@/Layouts/LineChart.vue';
import { computed } from 'vue';
import { useOnlineUsersStore } from '@/stores/online-store'

const onlineUsersStore = useOnlineUsersStore();


// Access state or getters directly
const getOnlineUsers = computed(() =>
    onlineUsersStore.getOnlineUsers
);


const props = defineProps({
    users: Number,
    storeQuery: Object,
    specialGcData: Object,
    regularGcData: Object
});

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// for special gc total sales counting per month
const specialGcData = computed(() => {
    let data = new Array(12).fill(0);

    if (props.specialGcData && Array.isArray(props.specialGcData)) {
        props.specialGcData.forEach(entry => {
            if (entry.month >= 1 && entry.month <= 12) {
                data[entry.month - 1] = entry.total_count;
            }
        });
    }
    return data;
})
//  regular gc data total sales counting per month
const regularGcData = computed(() => {
    let data = new Array(12).fill(0);

    if (props.regularGcData && Array.isArray(props.regularGcData)) {
        props.regularGcData.forEach(entry => {
            if (entry.month >= 1 && entry.month <= 12) {
                data[entry.month - 1] = entry.total_transactions;
            }
        });
    }
    return data;

});
const chartData = computed(() => {
    return {
        labels: months,
        datasets: [
            {
                label: 'Show Regular GC',
                backgroundColor: '#0047AB',
                borderColor: '#0047AB',
                fill: false,
                data: regularGcData.value
            },
            {
                label: 'Show Special GC',
                backgroundColor: '#29af19',
                borderColor: '#29af19',
                fill: false,
                data: specialGcData.value

            }
        ]
    };
});



const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true
        }
    }
}));
</script>

<style scoped>
.button-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.button-container .ant-btn {
    margin: 5px;
}

.card-hover {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.scrollable-list {
    max-height: 300px;
    /* Adjust the height as needed */
    overflow-y: auto;
    /* Enable vertical scrolling */
    border: 1px solid #f0f0f0;
    /* Optional: Add a border for better visibility */
    border-radius: 4px;
    /* Optional: Add border radius */
    padding: 8px;
    /* Optional: Add padding */
}

.card-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.buttons {
    width: 100%;
    background-color: #113961;
    height: 5rem;
    color: white;
    transition: transform 0.2s ease;
    cursor: pointer;
    margin: 0 5px;
}

.buttons:hover {
    background-color: #1b76f8;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.chart-container {
    text-align: center;
    width: 100%;
    height: auto;
    margin: auto;
}
</style>
