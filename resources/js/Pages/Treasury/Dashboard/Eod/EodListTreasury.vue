<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card :title="title">
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
                :data-source="data.data"
                :columns="columns"
                bordered
                :pagination="false"
            >
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
                    <template v-if="column.key === 'ieod_date'">
                        {{
                            dayjs(record.ieod_date).format(
                                "MMM DD, YYYY -  hh:mm a",
                            )
                        }}
                    </template>
                    <template v-if="column.key === 'eodBy'">
                        {{ record.user?.full_name }}
                    </template>

                    <template v-if="column.dataIndex === 'action'">
                        <a-space>
                            <!-- <a-button
                            type="primary"
                            size="small"
                            @click="viewRecord(record.ieod_id)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button> -->

                            <a-button
                                type="primary"
                                size="small"
                                ghost
                                @click="viewRecord(record.ieod_id)"
                            >
                                <template #icon>
                                    <AuditOutlined />
                                </template>
                                Reprint
                            </a-button>
                        </a-space>
                    </template>
                </template>
            </a-table>

            <pagination class="mt-5" :datarecords="data" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import axios from "axios";
import { highlighten } from "@/Mixin/UiUtilities";
import { ColumnTypes, FilterTypes, InstitutEod } from "@/types/treasury";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/core";
import { PaginationTypes } from "@/types";

const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    data: PaginationTypes<InstitutEod>;
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const form = ref({
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
});

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const viewRecord = async (id) => {
    const url = route("treasury.transactions.eod.pdf", { id: id });

    axios
        .get(url, { responseType: "blob" })
        .then((response) => {
            const file = new Blob([response.data], {
                type: "application/pdf",
            });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            if (error.response && error.response.status === 404) {
                alert("Pdf Not available");
            } else {
                console.error(error);
                alert("An error occurred while generating the PDF.");
            }
        });
};

watch(
    () => form.value,
    debounce(function () {
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
    }, 600),
    { deep: true },
);
</script>
