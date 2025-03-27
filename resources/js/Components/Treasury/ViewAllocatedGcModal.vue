<template>
    <a-modal
        :open="open"
        title="Allocated GC"
        style="width: 1000px"
        @cancel="handleClose"
        centered
        :footer="null"
    >
        <div class="mb-8 text-right">
            <a-input-search
                class="mr-1"
                v-model:value="searchValue"
                @change="filterSearch"
                placeholder="Search here..."
                style="width: 300px"
            />
        </div>
        <a-table
            bordered
            :pagination="false"
            :columns="allocatedGcColumn"
            :data-source="records.data"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], searchValue)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key == 'pro'">
                    <span
                        v-html="
                            highlightText(record.gc.pe_entry_gc, searchValue)
                        "
                    >
                    </span
                ></template>
                <template v-if="column.key == 'type'">
                    {{
                        record.loc_gc_type == 1 ? "Regular" : "Special"
                    }}</template
                >
                <template v-if="column.key == 'denom'">
                    <span
                        v-html="
                            highlightText(
                                record.gc.denomination.denomination,
                                searchValue
                            )
                        "
                    >
                    </span>
                </template>
            </template>
        </a-table>
        <pagination-axios
            :datarecords="records"
            @on-pagination="onChangePagination"
        />
    </a-modal>
</template>
<script setup lang="ts">
import axios from "axios";
import { ref, watch } from "vue";
import { highlighten } from "@/../../resources/js/Mixin/UiUtilities";

const { highlightText } = highlighten();

const props = defineProps<{
    open: boolean;
    store_id?: string | number;
    allocatedGcData: any;
}>();

const records= ref(props.allocatedGcData);
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();
const allocatedGcColumn = [
    {
        title: "Barcode #.",
        dataIndex: "loc_barcode_no",
    },
    {
        title: "Pro #.",
        key: "pro",
    },
    {
        title: "Type",
        key: "type",
    },
    {
        title: "Denomination",
        key: "denom",
    },
];
const searchValue = ref<string>("");
const handleClose = () => {
    emit("update:open", false);
};

const onChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        records.value = data;
    }
};
const filterSearch = async () => {
    const { data } = await axios.get(
        route("treasury.store.gc.viewAllocatedList", props.store_id),
        {
            params: {
                search: searchValue.value,
            },
        }
    );
    records.value = data;
};

watch(
    () => props.allocatedGcData,
    (newData) => {
        records.value = newData;
    },
    { immediate: true } 
);
</script>
