<template>
    <AuthenticatedLayout>
        <a-card>
            <a-input-search allow-clear enter-button placeholder="Input search here..." v-model:value="gcReceivedSearch"
                style="width:25%; margin-left: 75%;" />

            <a-table :data-source="record.data" :columns="columns" :pagination="false" size="small" bordered
                style="margin-top: 10px;">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-button type="primary" class="mr-1" @click="retreivedData(record.csrr_id)">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button type="primary" @click="reprint(record.recnumber)">
                            <template #icon>
                                <PrinterFilled />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-6" />
            <!-- <a-button @click="retreivedData">
                kupal
            </a-button> -->
            <received-gc-details-modal v-model:open="openModal" :data="data" />
            <!-- <a-modal v-model:open="open" @ok="okay">
                <span style="color:red">
                {{ searchMessage }}
                </span>
            </a-modal> -->
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';

defineProps({
    record: Object,
    columns: Array,
});

const data = ref({});
const openModal = ref(false);
const gcReceivedSearch = ref('');
// const searchMessage = ref('');

const retreivedData = async (id) => {
    await axios.get(route('iad.details.view', id)).then((res) => {
        data.value = res;
        openModal.value = true;
    })
}


watch(gcReceivedSearch, debounce(async (search) => {
    const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
    if (searchValidation.test(search)) {
        const openNotificationWithIcon = (type) => {
            notification[type]({
                message: 'Invalid input',
                description: 'Search contains invalid symbol or emojis',
                placement: 'topRight'
            });
        };
        openNotificationWithIcon('warning');
        return;
    }

    router.get(route('iad.view.received'), {
        search: search
    }, {
        preserveState: true
    })
}, 300))

// const reprint = (id) => {
//     router.get(route('iad.reprint.from.marketing',  id ), {
//         id
//     },{
//         preserveState: true
//     })
// }


// const reprint = (id) => {
//     axios.get(route('iad.reprint.from.marketing',id ), {
//         responseType: 'blob' // Important for handling binary data
//     })
//         .then(response => {
//             // Create a blob from the response data
//             const url = window.URL.createObjectURL(new Blob([response.data]));
//             // Create a link element
//             const link = document.createElement('a');
//             link.href = url;
//             // Set the download attribute with the desired file name
//             link.setAttribute('download', `0${id}.pdf`);
//             // Append the link to the body (required for Firefox)
//             document.body.appendChild(link);
//             // Trigger the download
//             link.click();
//             // Remove the link from the document
//             document.body.removeChild(link);
//         })
//         .catch(error => {
//             notification['error']({
//                 message: 'Notification Title',
//                 description:
//                     'This is the content of the notification. This is the content of the notification. This is the content of the notification.',
//             });
//         });
// };

const reprint = (id) => {
    const url = route("iad.reprint.from.marketing", {
                id: id,
            });

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
                        notification.error({
                            message: "Pdf not Found",
                            description:
                                "Pdf Not available in Marketing.",
                        });
                    } else {
                        console.error(error);
                        alert("An error occurred while generating the PDF.");
                    }
                });
}


</script>
