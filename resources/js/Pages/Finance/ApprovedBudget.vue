<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end">
                <a-button
                    @click="() => $inertia.visit(route('finance.dashboard'))"
                    class="mb-2"
                >
                    <RollbackOutlined />
                    Back to Dashboard
                </a-button>
            </div>
            <a-input-search
                allow-clear
                enter-button
                placeholder="Input search here..."
                v-model:value="approvedBudgetSearch"
                style="width: 25%; margin-left: 75%"
            />
            <a-table
                size="small"
                :data-source="record.data"
                :columns="columns"
                bordered
                :pagination="false"
                style="margin-top: 10px"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'viewing'">
                        <a-button @click="view(record.br_id)" class="mr-1">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button @click="reprint(record.br_no)">
                            <template #icon>
                                <PrinterOutlined />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-5" :datarecords="record" />
            <a-budget-details-modal
                v-model:open="viewModal"
                :selected="viewSelected"
            />
        </a-card>
        <a-modal v-model:open="open" @ok="okay">
            <span style="color: red">
                {{ searchMessage }}
            </span>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import { ref, watch } from "vue";
import { notification } from "ant-design-vue";
import { debounce } from "lodash";
import { router } from "@inertiajs/core";

defineProps({
    record: Object,
    columns: Array,
});

const viewModal = ref(false);
const viewSelected = ref({});
const approvedBudgetSearch = ref("");
const searchMessage = ref("");
const open = ref(false);

const okay = () => {
    open.value = false;
};

const view = async (id) => {
    await axios
        .get(route("finance.budget.approved.details", id))
        .then((res) => {
            viewSelected.value = res.data;
            viewModal.value = true;
        });
};
const reprint = (id) => {
    fetch(`/finance/reprint-${id}`)
        .then((response) => {
            if (!response.ok) {
                return response.json().then((errorData) => {
                    // console.log(errorData.status)
                    notification[errorData.status]({
                        message: errorData.title,
                        description: errorData.msg,
                    });
                });
            } else {
                // Handle successful file download
                window.location.href = `/finance/reprint-${id}`;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while processing your request.");
        });
};
watch(
    approvedBudgetSearch,
    debounce(async (search) => {
        const searchValidation =
            /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
        if (searchValidation.test(search)) {
            const openNotificationWithIcon = (type) => {
                notification[type]({
                    message: "Invalid input",
                    description: "Search contains invalid symbol or emojis",
                    placement: "topRight",
                });
            };
            openNotificationWithIcon("warning");
            return;
        }
        router.get(
            route("finance.budget.approved"),
            {
                search: search,
            },
            {
                preserveState: true,
            },
        );
    }, 300),
);
</script>
