<template>
    <AuthenticatedLayout>
        <div>
            <h2>Masterfile</h2>
        </div>
        <div
            style="display: flex; margin-top: 1rem; justify-content: center; align-items: center; flex-direction: column; ">
            <a-form-item>
                <a-button @click="() => $inertia.get(route('admin.masterfile.users'))" class="buttons">
                    <UserOutlined /> Setup Users
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.store.staff'))" class="buttons">
                    <AppstoreAddOutlined /> Setup Store Staff
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.customer.setup'))" class="buttons">
                    <CustomerServiceOutlined /> Setup Customer
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.setupStore'))" class="buttons">
                    <AppstoreFilled /> Setup Store
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.creditCardSetup'))" class="buttons">
                    <CreditCardFilled /> Setup Credit Card
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.denominationSetup'))" class="buttons">
                    <BarcodeOutlined /> Setup Denomination
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.revolvingFund'))" class="buttons">
                    <FundFilled /> Revolving Fund
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.tagHennan'))" class="buttons">
                    <TagOutlined /> Tag Hennan
                </a-button>

                <a-button @click="() => $inertia.get(route('admin.masterfile.blockBarcode'))" class="buttons"
                    style="margin-top: 1rem;">
                    <StopOutlined /> Blocked Barcode
                </a-button>
            </a-form-item>

        </div>
        <a-card>
            <div class="flex justify-center gap-5">
                <div class="text-center font-bold" v-for="item in props.data" :key="item.store_name">
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
        <!-- {{ data }} -->

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LineChart from '@/Layouts/LineChart.vue';
import { computed } from 'vue';


const props = defineProps({
    users: Number,
    data: Object,
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
//  regular gc data to sales counting per month
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

.card-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.buttons {
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
