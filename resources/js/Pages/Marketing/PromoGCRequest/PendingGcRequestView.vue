<template>
    <AuthenticatedLayout>
        <a-card title="Update Promo GC Request">
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card>
                        <a-form-item label="RFPROM No">
                            <a-input v-model:value="form.reqnum" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-date-picker style="width: 100%;" v-model:value="form.dateReq" />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-date-picker style="width: 100%;" v-model:value="form.dateNeed" />
                        </a-form-item>
                        <a-form-item label="Remarks">
                            <a-textarea v-model:value="form.remarks" />
                        </a-form-item>
                        <a-form-item label="Promo Group">
                            <a-select ref="select" v-model:value="form.group" style="width: 120px">
                                <a-select-option value="1">1</a-select-option>
                                <a-select-option value="2">2</a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-image style="height: 100px;" :src="'/storage/promoGcUpload/'+data[0]['pgcreq_doc']"></a-image>
                        <a-form-item>
                            <a-table :dataSource="denom" :columns="columns" :pagination="false">
                                <template v-slot:bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'qty'">
                                        <a-input v-model:value="record.qty" />
                                    </template>
                                </template>
                            </a-table>
                            <div>
                                <div class="flex justify-end">
                                    <a-button type="primary" @click="submit">
                                        submit
                                    </a-button>
                                </div>
                            </div>
                        </a-form-item>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card>
                        <a-card title="Total Promo GC Request">
                            <div style="font-size: 30px;">₱ {{ form.total.toFixed(2) }}</div>
                        </a-card>
                    </a-card>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import dayjs from 'dayjs';
import { ref, watch, onMounted } from 'vue'; // Import watch and onMounted
import pickBy from "lodash/pickBy";
import { useForm } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';

const props = defineProps({
    data: Object,
    denom: Array, // Define as array
    columns: Object,
    denomQty: Object
});

const form = useForm({
    reqnum: props.data[0]?.pgcreq_id,
    dateReq: dayjs(props.data[0]?.pgcreq_datereq),
    dateNeed: dayjs(props.data[0]?.pgcreq_datereq),
    remarks: props.data[0]?.pgcreq_remarks,
    group: props.data[0]?.pgcreq_group,
    total: 0 // Initialize total to 0
});

const calculateTotal = () => {
    form.total = props.denom.reduce((sum, record) => {
        const qty = record.qty || 0;
        const denomination = record.denomination || 0;
        return sum + (qty * denomination);
    }, 0);
};

const submit = () => {
    // Calculate the total before submitting
    calculateTotal();

    const denomination = props.denom.map((record) => ({
        quantity: record.qty,
        id: record.denom_id,
    }));

    form.transform((data) => ({
        ...data,
        date: dayjs(data.dateNeed).format('YYYY-MM-DD'),
        denom: pickBy(denomination)
    })).post(route('marketing.promoGcRequest.submit'), {
        onSuccess: (response) => {
            notification[response.props.flash.type]({
                message: response.props.flash.msg,
                description: response.props.flash.description,
            });
        }
    });
};

// Recalculate total when component is mounted
onMounted(() => {
    calculateTotal(); // Initial calculation when the component is mounted
});

// Watch for changes in the denom array to recalculate the total in real-time
watch(
    () => props.denom,
    () => {
        calculateTotal(); // Recalculate total whenever denom is updated
    },
    { deep: true } // Enable deep watching to track changes inside the denom array
);

</script>
