<template>
    <AuthenticatedLayout>
        <a-card>
            <div>
                <a-button @click="backButton" style="font-weight: bold;">
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
            <div v-if="loading" style="position: absolute; z-index: 1000; right: 0; left: 0; top: 3rem">
                <div class="spinnerContainer">
                    <div class="spinner"></div>
                    <div class="loader">
                        <p>loading</p>
                        <div class="words">
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                        </div>
                    </div>
                </div>
            </div>
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
                <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
                    <EditOutlined /> Update Tag
                </div>
                <div style="margin-top: 2rem; font-weight: bold;">
                    <div style="margin-top: 1rem">
                        <a-form-item for="tag" :validate-status="form.errors?.hennan_id ? 'error' : ''"
                            :help="form.errors?.hennan_id">
                            <span style="font-family: sans-serif; font-weight: bold">Tag :</span>
                            <a-select v-model:value="form.hennan_id" style="margin-top: 0.5rem">
                                <a-select-option v-for="item in fullname" :key="item.hennan_id"
                                    :value="item.hennan_id">{{
                                        item.fullname
                                    }}</a-select-option>
                            </a-select>
                        </a-form-item>
                    </div>
                </div>
            </a-modal>
            {{ search }}
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
    search: Array
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
.spinnerContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.spinner {
    width: 56px;
    height: 56px;
    display: grid;
    border: 4px solid #0000;
    border-radius: 50%;
    border-right-color: #299fff;
    animation: tri-spinner 1s infinite linear;
}

.spinner::before,
.spinner::after {
    content: "";
    grid-area: 1/1;
    margin: 2px;
    border: inherit;
    border-radius: 50%;
    animation: tri-spinner 2s infinite;
}

.spinner::after {
    margin: 8px;
    animation-duration: 3s;
}

@keyframes tri-spinner {
    100% {
        transform: rotate(1turn);
    }
}

.loader {
    color: #4a4a4a;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    font-size: 25px;
    -webkit-box-sizing: content-box;
    box-sizing: content-box;
    height: 40px;
    padding: 10px 10px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    border-radius: 8px;
}

.words {
    overflow: hidden;
}

.word {
    display: block;
    height: 100%;
    padding-left: 6px;
    color: #299fff;
    animation: cycle-words 5s infinite;
}

@keyframes cycle-words {
    10% {
        -webkit-transform: translateY(-105%);
        transform: translateY(-105%);
    }

    25% {
        -webkit-transform: translateY(-100%);
        transform: translateY(-100%);
    }

    35% {
        -webkit-transform: translateY(-205%);
        transform: translateY(-205%);
    }

    50% {
        -webkit-transform: translateY(-200%);
        transform: translateY(-200%);
    }

    60% {
        -webkit-transform: translateY(-305%);
        transform: translateY(-305%);
    }

    75% {
        -webkit-transform: translateY(-300%);
        transform: translateY(-300%);
    }

    85% {
        -webkit-transform: translateY(-405%);
        transform: translateY(-405%);
    }

    100% {
        -webkit-transform: translateY(-400%);
        transform: translateY(-400%);
    }
}
</style>
