<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <div class="flex justify-between mb-5">
                <div>
                    <a-range-picker v-model:value="form.date" />
                </div>
                <div>
                    <a-input-search
                        class="mr-1"
                        v-model:value="form.search"
                        placeholder="Search here..."
                        style="width: 300px"
                    />
                    <a-button type="primary" @click="visible = true">
                        <template #icon>
                            <UserAddOutlined />
                        </template>
                        Add Payment
                    </a-button>
                </div>
            </div>
            <a-table
                :data-source="data.data"
                :columns="columns"
                bordered
                size="small"
                :pagination="false"
            >
                <template #title>
                    <a-typography-title :level="4">{{
                        title
                    }}</a-typography-title>
                </template>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex">
                        <span
                            v-html="
                                highlightText(
                                    record[column.dataIndex],
                                    form.search,
                                )
                            "
                        >
                        </span>
                    </template>
                    <template v-if="column.key === 'user'">
                        <span
                            v-html="
                                highlightText(
                                    record.user.full_name,
                                    form.search,
                                )
                            "
                        >
                        </span>
                    </template>
                </template>
            </a-table>

            <pagination-resource class="mt-5" :datarecords="data" />
        </a-card>

        <a-modal
            v-model:open="visible"
            title="Add a Payment Fund"
            ok-text="Create"
            cancel-text="Cancel"
            @ok="onOk"
        >
            <a-form
                ref="formRef"
                :model="formState"
                layout="vertical"
                name="form_in_modal"
            >
                <a-form-item
                    name="paymentFundName"
                    label="Payment Fund Name"
                    has-feedback
                    :validate-status="
                        formState.invalid('paymentFundName') ? 'error' : ''
                    "
                    :help="formState.errors.paymentFundName"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Payment Fund Name',
                        },
                    ]"
                    @change="formState.validate('paymentFundName')"
                >
                    <a-input v-model:value="formState.paymentFundName" />
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import { highlighten } from "@/Mixin/UiUtilities";
import { useForm } from "laravel-precognition-vue";
import { onProgress } from "@/Mixin/UiUtilities";
import { router } from "@inertiajs/core";
import { computed, ref, watch } from "vue";
import { ColumnTypes, FilterTypes, PaymentFundTypes } from "@/types/treasury";
import { PaginationTypes } from "@/types";

const { openLeftNotification } = onProgress();
const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    data:  PaginationTypes<PaymentFundTypes[]>;
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const visible = ref(false);

const form = ref({
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
});

const formState = useForm("post", route("treasury.masterfile.addPaymentFund"), {
    paymentFundName: "",
});

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const onOk = () => {
    formState.submit({
        onSuccess: ({ data }) => {
            openLeftNotification(data);
            formState.reset();
            visible.value = false;
            router.visit(route(route().current()), { only: ["data"] });
        },
    });
};

const debouncedHandler = debounce(function () {
    const formattedDate = form.value.date
        ? form.value.date.map((date) => date.format("YYYY-MM-DD"))
        : [];

    router.get(
        route(route().current()),
        { ...pickBy(form.value), date: formattedDate },
        {
            preserveState: true,
        },
    );
}, 600);
watch(() => form.value, debouncedHandler, { deep: true });
</script>
