<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">

                <a-card>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date">{{ dayjs() }}</a-descriptions-item>
                    </a-descriptions>

                    <p class="mt-2 ml-1">
                        Payment to:
                    </p>
                    <a-select ref="select" v-model:value="form.payment" style="width: 100%"
                        placeholder="Select Payment Type">
                        <a-select-option value="STORE DEPARTMENT">STORE DEPARTMENT</a-select-option>
                        <a-select-option value="WHOLESALE">WHOLESALE</a-select-option>
                    </a-select>

                    <p class="mt-2 ml-1">
                        Customer
                    </p>
                    <a-select show-search placeholder="Search here..." :default-active-first-option="false"
                        v-model:value="form.customer" style="width: 100%" :show-arrow="false" :filter-option="false"
                        :not-found-content="isRetrieving ? undefined : null
                            " :options="optionCustomer" @search="debounceCustomer">
                        <template v-if="isRetrieving" #notFoundContent>
                            <a-spin size="small" />
                        </template>
                    </a-select>


                    <a-divider>Verify Barcode</a-divider>

                    <a-input-number v-model:value="form.barcode" @change="viewstatus" @keyup.enter="viewstatus"
                        style="width: 100%;" size="large" placeholder="Barcode Here" />

                    <div class="flex justify-center mt-4">
                        <p>
                            <a-typography-text keyboard>Verify By:</a-typography-text>
                        </p>
                        <p>
                            <a-typography-text keyboard>{{ $page.props.auth.user.full_name }}</a-typography-text>
                        </p>
                    </div>
                </a-card>
                <a-button type="primary" class="mt-1" :disabled="(form.barcode == null || form.barcode == '')
                    || (form.customer == null || form.customer == '')
                    || (form.payment == null || form.payment == '')" block @click="submit" :loading="form.processing">
                    <template #icon>
                        <FastForwardOutlined />
                    </template>
                    {{ form.processing ? 'Verifieng Barcode...' : 'Verify Barcode' }}
                </a-button>
                <!-- {{ notif.data }} -->
                <a-alert v-if="notif.status" :message="notif.msg" class="mb-1 mt-2" :type="notif.status" show-icon />
                <a-card v-if="notif.error == 'lost'" class="mt-5"
                    style="background-color: rgba(255, 0, 0, 0.1); border: solid 1px rgba(255, 0, 0, 0.6);">
                    <p class="text-center font-bold">
                        {{ notif.msg }}
                    </p>
                    <p>
                        Issue: Barcode # <span class="font-bold">{{ notif.data.lostgcb_barcode }}</span> was reported as
                        lost
                    </p>
                    <p>
                        Owner's Name: <span class="font-bold">{{ notif.data.lostgcd_owname }}</span>
                    </p>
                    <p>
                        Address: <span class="font-bold">{{ notif.data.lostgcd_address }}</span>
                    </p>
                    <p>
                        Contact No: <span class="font-bold">{{ notif.data.lostgcd_contactnum }}</span>
                    </p>
                    <p>
                        Date Lost: <span class="font-bold">{{ notif.data.lostgcd_datelost }}</span>
                    </p>
                    <p>
                        Date Reported: <span class="font-bold">{{ notif.data.lostgcd_datereported }}</span>
                    </p>
                </a-card>
            </a-col>
            <a-col :span="14">
                <a-alert v-if="success" :message="'Barcode # ' + form.barcode + ' found'" class="mb-1" type="success"
                    show-icon />
                <a-alert v-if="notfound" :message="'Barcode # ' + form.barcode + '  not found'" class="mb-1"
                    type="error" show-icon />
                <a-card v-if="!empty">
                    <div v-if="skeleton">
                        <a-skeleton avatar :paragraph="{ rows: 4 }" />
                    </div>
                    <div v-else>
                        <a-steps direction="vertical" style="color: green" :current="data.length - 1" :items="data">
                        </a-steps>

                    </div>
                </a-card>
                <div v-else-if="skeleton">
                    <a-skeleton avatar :paragraph="{ rows: 4 }" />
                </div>
                <a-card v-else>
                    <a-result status="404" title="Enter Barcode" sub-title="In order to show barcode status">
                    </a-result>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { Empty } from 'ant-design-vue';
const simpleImage = Empty.PRESENTED_IMAGE_SIMPLE;


const form = useForm({
    payment: null,
    customer: null,
    barcode: null,
})
const props = defineProps({
    data: Object,
    success: Boolean,
    notfound: Boolean,
    empty: Boolean,
})

const optionCustomer = ref([]);
const viewing = ref([]);
const notif = ref([]);
const isRetrieving = ref(false);
const skeleton = ref(false);
const status = ref({});

const submit = () => {
    form.transform((data) => ({
        ...data
    })).post(route('retail.verification.submit'), {
        onSuccess: (response) => {

            notif.value = response.props.flash

            notification[response.props.flash.status]({
                message: response.props.flash.title,
                description: response.props.flash.msg,
            });
        }
    });
}

const debounceCustomer = debounce(async function (query) {
    try {
        if (query.trim().length) {

            // isRetrieving.value = true;
            optionCustomer.value = [];

            const { data } = await axios.get(
                route("search.customer", { search: query })
            );

            optionCustomer.value = data.map((data) => ({
                title: data.full_name,
                value: data.cus_id,
                label: data.full_name,
            }));

            viewing.value = optionCustomer.value.length ? optionCustomer.value[0] : null;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        isRetrieving.value = false;
    }
}, 500);

const viewstatus = () => {
    skeleton.value = true;
    router.get(route("retail.verification.index"), {
        barcode: form.barcode,

    }, {
        onSuccess: () => {
            skeleton.value = false;
        },
        preserveState: true
    });
}

</script>
