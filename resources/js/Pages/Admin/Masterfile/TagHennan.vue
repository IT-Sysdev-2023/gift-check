<template>
    <AuthenticatedLayout>
        <a-card>
            <div>
                <a-button @click="backButton" style="font-weight: bold; position: absolute; right: 2rem">
                    <RollbackOutlined /> Back
                </a-button>
            </div>
            <div style="
                    margin-top: 1rem;
                    font-family: sans-serif;
                    font-size: 1.5rem;
                    font-weight: bold;
                ">
                <span>
                    Tag Hennan Setup
                </span>
            </div>
            <div>
                <a-input-search allow-clear @change="searchFunction" v-model:value="tagHennanSearch" size="medium"
                    enter-button placeholder="Input search here..." style="width: 25%; margin-left: 70%" />
            </div>
            <span v-if="loading" style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                ">
                <div class="loader">
                    <div class="loaderMiniContainer">
                        <div class="barContainer">
                            <span class="bar"></span>
                            <span class="bar bar2"></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 101 114" class="svgIcon">
                            <circle stroke-width="7" stroke="black" transform="rotate(36.0692 46.1726 46.1727)"
                                r="29.5497" cy="46.1727" cx="46.1726"></circle>
                            <line stroke-width="7" stroke="black" y2="111.784" x2="97.7088" y1="67.7837" x1="61.7089">
                            </line>
                        </svg>
                    </div>
                </div>

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
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
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

const searchFunction = () => {
    router.get(route('admin.masterfile.tagHennan'), {
        searchvalue: tagHennanSearch.value
    },
        {
            onStart: () => {
                loading.value = true

            },
            onSuccess: () => {
                loading.value = false

            },
            onError: () => {
                loading.value = false
            },

            preserveState: true
        }
    )
}

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
</script>

<style scoped>
.loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.loaderMiniContainer {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 130px;
    height: fit-content;
}

.barContainer {
    width: 100%;
    height: fit-content;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: 10px;
    background-position: left;
}

.bar {
    width: 100%;
    height: 8px;
    background: linear-gradient(to right,
            rgb(161, 94, 255),
            rgb(217, 190, 255),
            rgb(161, 94, 255));
    background-size: 200% 100%;
    border-radius: 10px;
    animation: bar ease-in-out 3s infinite alternate-reverse;
}

@keyframes bar {
    0% {
        background-position: left;
    }

    100% {
        background-position: right;
    }
}

.bar2 {
    width: 50%;
}

.svgIcon {
    position: absolute;
    left: -25px;
    margin-top: 18px;
    z-index: 2;
    width: 70%;
    animation: search ease-in-out 3s infinite alternate-reverse;
}

@keyframes search {
    0% {
        transform: translateX(0%) rotate(70deg);
    }

    100% {
        transform: translateX(100px) rotate(10deg);
    }
}

.svgIcon circle,
line {
    stroke: rgb(162, 55, 255);
}

.svgIcon circle {
    fill: rgba(98, 65, 142, 0.238);
}
</style>
