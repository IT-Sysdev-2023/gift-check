<template>
    <AuthenticatedLayout>
        <a-card>
            <a-row class="mb-3">
                <a-col :span="16">
                    <a-space>
                        <a-radio-group @change="handleChange" v-model:value="radio">
                            <a-radio :style="radioStyle" :value="1">Date</a-radio>
                            <a-radio :style="radioStyle" :value="2">Date Range</a-radio>
                        </a-radio-group>
                    </a-space>
                    <a-space>
                        <div v-if="date">
                            <a-date-picker @change="handleDateChange" style="width: 250px;" />
                        </div>
                        <div v-else-if="dateRange">
                            <a-range-picker @change="handleDateRangeChange" />
                        </div>
                        <a-select ref="select" @change="handleChangeBunit" placeholder="Select Store"
                            style="width: 200px">
                            <a-select-option v-for="store in stores" v-model:value="store.store_id">{{ store.store_name
                                }}</a-select-option>
                        </a-select>
                        
                    </a-space>
                </a-col>
                <a-col :span="8">
                    <div class="flex justify-end">
                        <a-button @click="generate">
                            <template #icon>
                                <PrinterOutlined />
                            </template>
                            Generate Eod Excel
                        </a-button>
                    </div>
                </a-col>
            </a-row>

            <a-table bordered :data-source="record.data" :pagination="false" :columns="columns" size="small"></a-table>

            <pagination :datarecords="record" class="mt-5"/>

        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    record: Object,
    stores: Object
})

const radio = ref(null);
const date = ref(false);
const dateRange = ref(false);

const dt = ref(null)
const st = ref(null)

const handleChange = (key) => {
    if (key.target.value === 1) {
        date.value = true;
        dateRange.value = false;
    } else {
        date.value = false;
        dateRange.value = true;
    }
}

const handleDateChange = (obj, str) => {
    dt.value = str;
    router.get(route('admin.eod.reports'), {
        date: str,
        store: st.value
    }, {
        preserveState: true
    })
}

const handleDateRangeChange = (obj, str) => {
    dt.value = str;
    router.get(route('admin.eod.reports'), {
        date: str,
        store: st.value
    }, {
        preserveState: true
    })
}
const handleChangeBunit = (store) => {
    st.value = store;
    router.get(route('admin.eod.reports'), {
        store: store,
        date: dt.value
    }, {
        preserveState: true
    })
}

const columns = ref([
    {
        title: 'Transaction No',
        dataIndex: 'trans_number',
        key: 'name',
    },
    {
        title: 'Trans Store',
        dataIndex: 'store_name',
        key: 'age',
    },
    {
        title: 'Trans Date',
        dataIndex: 'trans_datetime',
        key: 'address',
    },
    {
        title: 'Trans Cashier',
        dataIndex: 'ss_username',
        key: 'address',
    },

]);

const generate = () => {
    router.get(route('admin.generate'), {
        date: dt.value,
        store: st.value,
    })
}

</script>
