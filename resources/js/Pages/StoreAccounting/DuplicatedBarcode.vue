<template>
    <a-card>
        <a-tabs>
            <a-tab-pane key="1">
                <template #tab>
                    <span style="font-weight: bold;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        Duplicated Report (CEBU)
                    </span>
                </template>
                <a-card style="width: 25%;">
                    <div style="margin-left: 50px;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        <span style="font-weight: bold;">
                            Duplicate Barcodes
                        </span>
                        <div style="margin-left: 50px; color:#1e90ff; font-weight: bold;">
                            Cebu
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <span :style="{ fontSize: iconSize + 'px' }">
                            <FileSearchOutlined />
                        </span>
                        <input type="file" ref="fileInput" style="display: none;" @change="handleFileChange" />

                        <a-button @click="ChooseFileCebu" style="background-color: #1e90ff; color: white;">
                            <LoadingOutlined />
                            Choose File
                        </a-button>
                        <div v-if="!cebuFileName && !message" style="color:red;">
                            No file selected

                        </div>
                        <div v-if="!cebuFileName" style="color:red">
                            {{ this.message }}
                        </div>
                        <div v-if="cebuFileName" style="width: 40%; max-width: 40%;">
                            <span style="color:#1e90ff; font-weight: bold;">
                                Selected File:
                            </span>
                            <div style="text-decoration: underline;">
                                {{ cebuFileName }}
                            </div>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-button @click="cebuReportButton" style="background-color: green; color: white">
                                <CheckOutlined />
                                Check Duplicates
                            </a-button>
                        </div>

                        <div style="color:red; margin-top: 20px;">
                            <WarningOutlined />
                            Note: Upload Textfile Only.
                        </div>
                    </div>
                </a-card>

            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span style="font-weight: bold;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        Duplicated Report (ALTA CITTA)
                    </span>
                </template>
                <a-card style="width: 25%">
                    <div style="margin-left: 50px;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        <span style="font-weight: bold;">
                            Duplicate Barcodes
                        </span>
                        <div style="margin-left: 50px; color:#1e90ff; font-weight: bold;">
                            Alta Citta
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <span :style="{ fontSize: iconSize + 'px' }">
                            <FileSearchOutlined />
                        </span>
                        <input type="file" ref="fileInput" style="display: none;" @change="handleAltaCittaFileChange" />

                        <a-button @click="chooseFileAltaCitta" style="background-color: #1e90ff; color: white;">
                            <LoadingOutlined />
                            Choose File
                        </a-button>
                        <div v-if="!altaCittaFileName">
                            <span style="color:red">
                                No file selected
                            </span>
                        </div>
                        <div v-if="altaCittaFileName" style=" width: 40%; max-width: 40%;">
                            <span style="color:#1e90ff; font-weight: bold;">
                                Selected file:
                            </span>
                            <div style="text-decoration: underline">
                                {{ altaCittaFileName }}
                            </div>
                        </div>


                        <div style="margin-top: 20px;">
                            <a-button @click="altaCittaReportButton" style="background-color: green; color: white">
                                <CheckOutlined />
                                Check Duplicates
                            </a-button>
                        </div>

                        <div style="color:red; margin-top: 20px;">
                            <WarningOutlined />
                            Note: Upload Textfile Only.
                        </div>
                    </div>
                </a-card>

            </a-tab-pane>
        </a-tabs>
    </a-card>

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ExclamationCircleOutlined, WindowsFilled } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { message, Modal } from 'ant-design-vue';
import { notification } from 'ant-design-vue';
import TextfileUploader from '../Custodian/TextfileUploader.vue';

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            message: '',
            cebuFileName: '',
            altaCittaFileName: '',
            iconSize: 80,
        }
    },
    methods: {
        ChooseFileCebu() {
            this.$refs.fileInput.click();
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            this.cebuFileName = file ? file.name : '';

        },
        chooseFileAltaCitta() {
            this.$refs.fileInput.click();
        },
        handleAltaCittaFileChange(event) {
            const file = event.target.files[0];
            this.altaCittaFileName = file ? file.name : '';
        },
        cebuReportButton() {

            if (!this.cebuFileName) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before checking duplicates!',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }

            const openNotificationWithIcon = (type) => {
                notification[type]({
                    message: 'Invalid file selected',
                    description: 'Invalid file selected, please select a text file only!',
                    placement: 'topRight'
                });
            };

            const validTextFileExtensions = ['txt', 'csv', 'tsv', 'log', 'json', 'xml', 'html', 'markdown', 'rtf'];

            const fileExtension = this.cebuFileName.split('.').pop().toLowerCase();

            if (!validTextFileExtensions.includes(fileExtension)) {
                openNotificationWithIcon('warning');
                return;
            }
            const cebuData = {
                data: this.cebuFileName
            }

            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to check DUPLICATE BARCODE?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.duplicateExcel', cebuData);
                    setTimeout(hide, 1500);
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
       
        altaCittaReportButton() {

            if (!this.altaCittaFileName) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before checking duplicates!',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
            const openNotificationWithIcon = (type) => {
                notification[type]({
                    message: 'Invalid file selected',
                    description: 'Invalid file selected, please select a text file only!',
                    placement: 'topRight'
                });
            };
            const validTextFileExtensions = ['txt', 'csv', 'tsv', 'log', 'json', 'xml', 'html', 'markdown', 'rtf'];

            const fileExtension = this.cebuFileName.split('.').pop().toLowerCase();

            if (!validTextFileExtensions.includes(fileExtension)) {
                openNotificationWithIcon('warning');
                return;
            }

            const altaCittaData = {
                data: this.altaCittaFileName
            }

            Modal.confirm({
                title: 'Confirmation',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'Are you sure you want to check DUPLICATE BARCODE?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.duplicateExcel', altaCittaData)
                    setTimeout(hide, 1500);
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        }


    }
}
</script>
