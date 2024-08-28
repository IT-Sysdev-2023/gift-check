<template>
    <AuthenticatedLayout>
        <a-row>
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

                    <a-input-number v-model:value="form.barcode" @keyup.enter="viewstatus" style="width: 100%;"
                        size="large" placeholder="Barcode Here" />

                    <div class="flex justify-center mt-4">
                        <p>
                            <a-typography-text keyboard>Verify By:</a-typography-text>
                        </p>
                        <p>
                            <a-typography-text keyboard>{{ $page.props.auth.user.full_name }}</a-typography-text>
                        </p>
                    </div>
                </a-card>
                <a-button type="primary" class="mt-1" block @click="submit">
                    <template #icon>
                        <FastForwardOutlined />
                    </template>
                    Submit
                </a-button>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <a-steps direction="vertical" style="color: green" :current="data.length - 1" :items="data">
                    </a-steps>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import debounce from 'lodash/debounce';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';

const form = useForm({
    payment: null,
    customer: null,
    barcode: null,
})
const props = defineProps({
    data: Object,
})

const optionCustomer = ref([]);
const viewing = ref([]);
const isRetrieving = ref(false);

const submit = () => {
    form.transform((data) => ({
        ...data
    })).post(route('retail.verification.submit'));
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
}, 1000);

const viewstatus = () => {

    router.get(route("retail.verification.index"), {
        barcode: form.barcode,

    }, {
        onSuccess: () => {
        },
        preserveState: true
    });
}

</script>
