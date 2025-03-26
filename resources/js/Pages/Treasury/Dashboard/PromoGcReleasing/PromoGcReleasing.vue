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
                </div>
            </div>
            <a-table
                class="mt-10"
                bordered
                size="small"
                :pagination="false"
                :columns="columns"
                :data-source="data.data"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'pgcreq_reqnum'">
                        {{ record.promo_gc_request.pgcreq_reqnum }}
                    </template>
                    <template v-if="column.key == 'relby'">
                        {{ record.user.full_name }}
                    </template>
                    <template v-if="column.key == 'action'">
                        <a-space>
                            <a-button
                                type="primary"
                                ghost
                                @click="viewRecord(record.prrelto_id)"
                                >View</a-button
                            >
                            <!-- <a-button
                                type="primary"
                                @click="viewRecord(record.prrelto_id)"
                                >Reprint</a-button
                            > -->
                        </a-space>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" />
        </a-card>
        <a-modal
            v-model:open="openModal"
            width="1500px"
            :footer="false"
            centered
        >
            <a-card :title="title + ' details'" style="text-align: center">
                <a-form layout="horizontal" style="min-width: 600px">
                    <a-row :gutter="[16, 0]">
                        <a-col :span="12"
                            ><FormItem
                                label="Date Released:"
                                :value="formatDate(viewedData.prrelto_date)"
                            />
                            <a-flex align="center" class="space-x-10">
                                <FormItem
                                    label="Remarks:"
                                    :value="viewedData.prrelto_remarks"
                                />

                                <FormItem
                                    label="Received By:"
                                    :value="viewedData.prrelto_recby"
                                />
                            </a-flex>

                            <FormItem
                                label="Checked By:"
                                :value="viewedData.prrelto_checkedby"
                            />

                            <FormItem
                                label="Released by:"
                                :value="viewedData.user.full_name"
                            />
                        </a-col>
                        <a-col :span="12">
                            <!-- <a-form-item
                                label="Document Uploaded:"
                                v-if="viewedData.prrelto_docs"
                            >
                                <a-button type="primary" ghost>
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                    {{ viewedData.prrelto_docs }}</a-button
                                >
                            </a-form-item> -->
                            <a-form-item label="Document Uploaded.:">
                                <ant-image-preview
                                    :images="[
                                        {
                                            name: viewedData.prrelto_docs,
                                            url:
                                                '/storage/' +
                                                viewedData.prrelto_docs,
                                        },
                                    ]"
                                />
                            </a-form-item>
                            <FormItem
                                label="Released Type:"
                                :value="viewedData.prrelto_status"
                            />
                            <FormItem
                                label="Approved by:"
                                :value="viewedData.prrelto_approvedby"
                            />
                            <FormItem
                                label="Total Gc Amount:"
                                :value="viewedData.user.full_name"
                            />
                        </a-col>
                    </a-row>
                </a-form>
            </a-card>

            <a-card title="Released GC">
                <a-table
                    bordered
                    :dataSource="denomination.data"
                    :pagination="false"
                    size="small"
                    :columns="[
                        {
                            title: 'Barcode',
                            dataIndex: 'prreltoi_barcode',
                        },
                        {
                            title: 'Denomination',
                            dataIndex: 'denomination',
                        },
                    ]"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'unit'">
                            pc(s)
                        </template>
                    </template>
                    <template #summary>
                        <a-table-summary-row>
                            <a-table-summary-cell>Total</a-table-summary-cell>
                            <a-table-summary-cell>
                                <a-typography-text type="danger">{{
                                    totals
                                }}</a-typography-text>
                            </a-table-summary-cell>
                        </a-table-summary-row>
                    </template>
                </a-table>
                <pagination-axios
                    :datarecords="denomination"
                    @on-pagination="onChangeDenominationPagination"
                />
            </a-card>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import { computed, ref, watch } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import { PaginationTypes } from "@/types";

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const props = defineProps<{
    title: string;
    data: PaginationTypes;
    columns: any[];
    filters: {
        search: string;
        date: string | any[];
    };
}>();
const onChangeDenominationPagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        denomination.value = data.denomination;
    }
};
const formatDate = (date) => dayjs(date).format("MMM D, YYYY h:mm A");
const openModal = ref<boolean>(false);
const viewedData = ref(null);
const denomination = ref(null);
const totals = ref<number>(0);
const form = {
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
};

const viewRecord = async (id) => {
    const { data } = await axios.get(
        route("treasury.promo.gc.viewReleased", id)
    );
    viewedData.value = data.data;
    denomination.value = data.denomination;
    totals.value = data.total;
    openModal.value = true;
};

watch(
    () => form, // Getter function to track the form object
    debounce(() => {
        const formattedDate = form.date
            ? form.date.map((date) => date.format("YYYY-MM-DD"))
            : [];

        router.get(
            route(route().current()),
            { ...pickBy(form), date: formattedDate },
            {
                preserveState: true,
            }
        );
    }, 600),
    { deep: true } // Options object
);
</script>
