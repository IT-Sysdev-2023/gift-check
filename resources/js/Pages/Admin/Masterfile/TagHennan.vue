<template>
    <AuthenticatedLayout>
        <a-card>
            <div>
                <a-button @click="backButton" style="font-weight: bold">
                    <RollbackOutlined /> Back
                </a-button>
            </div>
            <div style="
                    margin-top: 1rem;
                    font-family: sans-serif;
                    font-size: 1.5rem;
                    font-weight: bold;
                ">
                <span>Tag Hennan Setup</span>
            </div>
            <div>
                <a-input-search allow-clear enter-button v-model:value="tagHennanSearch" size="medium"
                    placeholder="Input search here..." style="width: 25%; margin-left: 70%" />
            </div>
            <span v-if="loading" style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                ">
                <section class="dots-container">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </section>
            </span>
            <div style="margin-top: 1rem">
                <a-table :columns="columns" :data-source="data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateTagHennan(record)" class="me-2 me-sm-5"
                                style="color: white; background-color: green">
                                <EditOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </div>
            <a-modal v-model:open="updateModal" @ok="submitUpdatedTag">
                <span style="color: #0286df; font-size: 17px">
                    <EditOutlined /> Update Tag
                </span>
                <div style="margin-top: 1rem">
                    <a-form-item for="tag" :validate-status="form.errors?.hennan_id ? 'error' : ''"
                        :help="form.errors?.hennan_id">
                        <span style="font-family: sans-serif; font-weight: bold">Tag :</span>
                        <a-select v-model:value="form.hennan_id" style="margin-top: 0.5rem">
                            <a-select-option v-for="item in fullname" :key="item.hennan_id" :value="item.hennan_id">{{
                                item.fullname
                                }}</a-select-option>
                        </a-select>
                    </a-form-item>
                </div>
            </a-modal>
            <a-modal v-model:open="search">
                <h1>NO FUNCTION YET, ITS UNDER CONSTRUCTION</h1>
            </a-modal>
            <!-- {{ data }} -->
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import { ref, watch } from "vue";
import { router } from "@inertiajs/core";
import { notification } from "ant-design-vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { createVNode } from "vue";
import { Modal } from "ant-design-vue";

defineProps({
    data: Object,
    fullname: Object,
});
const loading = ref(false);
const search = ref(false);
const tagHennanSearch = ref("");
const form = ref([
    {
        fullname: "",
        tag: "",
        hennan_id: "",
        spexgcemp_barcode: "",
    },
]);

const updateModal = ref(false);

const columns = ref([
    {
        title: "Barcode",
        dataIndex: "spexgcemp_barcode",
    },
    {
        title: "Tag",
        dataIndex: "fullname",
    },
    {
        title: "Action",
        dataIndex: "action",
    },
]);

const backButton = () => {
    router.get(route("admin.dashboard"));
};

const updateTagHennan = (data) => {
    updateModal.value = true;
    form.value = { ...data };
};

const submitUpdatedTag = () => {
    updateModal.value = false;
    Modal.confirm({
        title: "Confirmation",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            "div",
            {
                style: "color:red;",
            },
            "Are you sure you want to update tag?",
        ),
        onOk: () => {
            router.get(route("admin.masterfile.updateTagHennan"), {
                spexgcemp_barcode: form.value.spexgcemp_barcode,
                hennan_id: form.value.hennan_id,
            });
            notification.success({
                message: "Success",
                description: "Tag updated successfully!",
            });
        },
        onCancel: () => {
            updateModal.value = false;
        },
        class: "test",
    });
};

watch(tagHennanSearch, () => {
    search.value = true;
})

</script>

<style scoped>
.dots-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    z-index: 100;
}

.dot {
    height: 20px;
    width: 20px;
    margin-right: 10px;
    border-radius: 10px;
    background-color: #b3d4fc;
    animation: pulse 1.5s infinite ease-in-out;
    z-index: 100;
}

.dot:last-child {
    margin-right: 0;
}

.dot:nth-child(1) {
    animation-delay: -0.3s;
}

.dot:nth-child(2) {
    animation-delay: -0.1s;
}

.dot:nth-child(3) {
    animation-delay: 0.1s;
}

@keyframes pulse {
    0% {
        transform: scale(0.8);
        background-color: #b3d4fc;
        box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
    }

    50% {
        transform: scale(1.2);
        background-color: #6793fb;
        box-shadow: 0 0 0 10px rgba(178, 212, 252, 0);
    }

    100% {
        transform: scale(0.8);
        background-color: #b3d4fc;
        box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
    }
}
</style>
