<template>
    <a-modal style="width: 80%; top: 40px" :mask-closable="false" class="text-center" title="Create Entry Store"
        :footer="null">
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="7">
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Receiving No.">{{ record.store
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Received">{{ dayjs().format('MMM, DD YYYY')
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Request No">{{
                            record.approved.store_gc_request.sgc_num }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Requested">{{ record.approved.dateReq
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Released No.">{{
                            record.approved?.agcr_request_relnum }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Dated Released">{{ record.approved.dateApp
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Released By">{{ record.approved.user.full_name
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received Type">{{ record.approved.type
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received by">{{ $page.props.auth.user.full_name
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <span>
                        <a-typography-text keyboard>Check By</a-typography-text>
                    </span>
                    <a-input v-model:value="form.checkby" allow-clear>
                    </a-input>
                </a-col>
                <a-col :span="17">
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 20%;" label="Domination">
                            Quantity</a-descriptions-item>
                        <a-descriptions-item style="width: 20%;" label="Subtotal">Scanned</a-descriptions-item>
                        <a-descriptions-item>Action</a-descriptions-item>
                    </a-descriptions>
                    <div v-for="item in record.release">
                        <a-descriptions class="mb-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 20%;" :label="'₱ ' + item[0].denom.toLocaleString()">{{
                                item[0].quantity }}</a-descriptions-item>
                            <a-descriptions-item style="width: 20%;" :label="'₱ ' + item[0].sub.toLocaleString()">{{
                                item[0].scanned }}</a-descriptions-item>
                            <a-descriptions-item>
                                <a-button @click="openDrawer(item)" :disabled="item[0].scanned === item[0].quantity">
                                    <template #icon>
                                        <FastForwardOutlined />
                                    </template>
                                    Scan Gc
                                </a-button>
                            </a-descriptions-item>
                        </a-descriptions>
                    </div>
                    <a-row :gutter="[16, 16]" class="mt-3">
                        <a-col :span="12">
                            <span>
                                Total
                            </span>
                            <a-input class="text-center" size="large" readonly :value="record.total" />
                        </a-col>
                        <a-col :span="12">
                            <span>
                                Scanned Gc
                            </span>
                            <a-input class="text-center" size="large" readonly :value="record.totscanned" />
                        </a-col>
                    </a-row>

                </a-col>
            </a-row>

            <div class="flex justify-end">
                <a-button  class="mr-1">
                    <template #icon>
                        <KeyOutlined />
                    </template>
                    Manager's Key
                </a-button>
                <a-button type="primary"  @click="submit" :disabled="(form.checkby == null || form.checkby == '') || (record.totquant !== record.totscanned)"
                   >
                    <template #icon>
                        <StepForwardOutlined />
                    </template>
                    Submit Entry Store
                </a-button>
            </div>
            <p v-if="(form.checkby == null || form.checkby == '') || (record.totquant !== record.totscanned)" class="text-end mt-2 animate-pulse"
                style="color:  red ; font-size: 12px;">
                Note: *please fill all the requirements to enable submit
            </p>
        </a-card>

        <scan-gc-drawer v-model:open="drawer" @close-drawer="closeDrawer" :data="record" :record="data"
            :rec="record.store" />

    </a-modal>

</template>

<script setup>

import { useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';


const props = defineProps({
    record: Object,
})

const form = useForm({
    recnum: props.record.store,
    relnum: props.record.approved?.agcr_request_relnum,
    checkby: null,
    total: props.record.total
})

const drawer = ref(false)

const data = ref({})

const emit = defineEmits(['close-modal']);

const openDrawer = (record) => {
    drawer.value = true;
    data.value = record[0];
}
const closeDrawer = (record) => {
    drawer.value = false;
}

const submit = () => {
    form.transform((data) => ({
        ...data
    })).post(route('retail.manage.submit'), {
        onSuccess: (response) => {
            notification[response.props.flash.status]({
                message: response.props.flash.title,
                description: response.props.flash.msg,
            });
            if(response.props.flash.status == 'success'){
                emit('close-modal')
            }
        },
        preserveState: true,
    });
}


</script>
