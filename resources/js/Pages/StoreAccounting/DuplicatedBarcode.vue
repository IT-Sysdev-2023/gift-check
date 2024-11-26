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

                        <a-input type="file" id="barcodeFile" @change="handleFileChange"
                            style="border: 2px solid #1e90ff; font-weight: bold; color:white; font-style: oblique; width: 117px;" />
                    </div>
                    <div style="margin-top: 10px;">
                        <span v-if="textfile" style="color:green; font-weight: bold;">Selected TextFile: </span>
                        <span style="margin-left: 5px;">
                            {{ this.textfile }}
                        </span>
                        <span v-if="!textfile" style="color:red">

                            No selected Textfile
                        </span>
                    </div>
                    <div>
                        <a-button @click="cebuReportButton"
                            style="background-color: #1e90ff; color: white; margin-top: 20px;">
                            <FileExcelOutlined />
                            Check Duplicates
                        </a-button>
                    </div>
                </a-card>
                <!-- {{ this.message }} -->
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span style="font-weight: bold;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        Duplicated Report (ALTTA CITTA)
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
                        <div style="margin-left: 45px; color:#1e90ff; font-weight: bold;">
                            ALTTA CITTA
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <span :style="{ fontSize: iconSize + 'px' }">
                            <FileSearchOutlined />
                        </span>

                        <a-input type="file" id="barcodeFile" @change="handleFileChangeAltta"
                            style="border: 2px solid #1e90ff; font-weight: bold; color:white; font-style: oblique; width: 117px;" />
                    </div>
                    <div style="margin-top: 10px;">
                        <span v-if="altaTextFile" style="color:green; font-weight: bold;">Selected TextFile: </span>
                        <span style="margin-left: 5px;">
                            {{ this.altaTextFile }}
                        </span>
                        <span v-if="!altaTextFile" style="color:red">

                            No selected Textfile
                        </span>
                    </div>
                    <div>
                        <a-button @click="alttaReportButton"
                            style="background-color: #1e90ff; color: white; margin-top: 20px;">
                            <FileExcelOutlined />
                            Check Duplicates
                        </a-button>
                    </div>
                </a-card>
                {{ tagbilaran }}
            </a-tab-pane>
        </a-tabs>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TagsOutlined, FileSearchOutlined, CheckOutlined, PlayCircleFilled } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import Description from '../Treasury/Description.vue';
import { placements } from 'ant-design-vue/es/vc-tour/placements';
import { message, Modal } from 'ant-design-vue';
import ColumnGroup from 'ant-design-vue/es/vc-table/sugar/ColumnGroup';


export default {
    layout: AuthenticatedLayout,
    props: {
        tagbilaran: Object
    },
    data() {
        return {
            cebuFileName: '',
            iconSize: 80,
            fileContent: '',
            barcodes: [],
            textfile: '',

            altaTextFile: '',
            altaContent: '',
            altaBarcode: []
        };
    },
    methods: {
        handleFileChangeAltta(event) {
            const file = event.target.files[0];
            this.altaTextFile = file.name

            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.altaContent = e.target.result;
                    console.log(this.altaContent);

                    this.altaBarcode = this.extractBarcodesAltta(this.altaContent);
                    console.log(this.altaBarcode);
                };
                reader.readAsText(file);
            }
        },
        extractBarcodesAltta() {
            const regex = /\b\d{1,20}\b/g;
            const barcodes = content.match(regex);
            return barcodes ? barcodes.map(barcode => barcode.trim().replace(/\t/g, '')) : [];
        },
        alttaReportButton() {
            if (this.altaContent) {
                const altaBarcodeString = this.altaContent

                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'Invalid File Selected',
                        description: 'Please select Textfile only',
                        placement: 'topRight'
                    });
                };
                const validTextFileAltta = ['txt', 'csv', 'tsv', 'log', 'json', 'xml', 'html', 'markdown', 'rtf'];
                const fileExtention = this.altaTextFile.split('.').pop().toLowerCase();

                if (!validTextFileAltta.includes(fileExtention)) {
                    openNotificationWithIcon('warning');
                    return;

                }
                Modal.confirm({
                    title: 'Notification',
                    content: 'Are you sure you want to check DUPLICATE BARCODES?',
                    okText: 'Yes',
                    cancelText: 'No',
                    onOk: () => {
                        window.location.href = route('storeaccounting.duplicateExcel', { barcodes: altaBarcodeString });
                    },
                    onCancel: () => {
                        console.log('Cancel');
                    }
                });

            }
            else {
                const notificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first or the selected Textfile has no data found',
                        placement: 'topRight'
                    });
                };
                notificationWithIcon('warning');
                return;
            }

    },

    handleFileChange(event) {
        const file = event.target.files[0];
        // console.log(file.name);
        this.textfile = file.name
        if (file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.fileContent = e.target.result;
                console.log(this.fileContent);


                this.barcodes = this.extractBarcodes(this.fileContent);
                console.log(this.barcodes);
            };

            reader.readAsText(file);
        }


    },

    extractBarcodes(content) {
        const regex = /\b\d{1,20}\b/g;
        const barcodes = content.match(regex);
        return barcodes ? barcodes.map(barcode => barcode.trim().replace(/\t/g, '')) : [];
    },

    cebuReportButton() {
        if (this.fileContent) {
            const barcodeString = this.fileContent

            const openNotificationWithIcon = (type) => {
                notification[type]({
                    message: 'Invalid File Selected',
                    description: 'Please select Textfile only',
                    placement: 'topRight',
                });
            };
            const validFileExtention = ['txt', 'csv', 'tsv', 'log', 'json', 'xml', 'html', 'markdown', 'rtf'];
            const fileExtention = this.textfile.split('.').pop().toLowerCase();

            if (!validFileExtention.includes(fileExtention)) {
                openNotificationWithIcon('warning');
                return;
            }

            Modal.confirm({
                title: 'Notification',
                content: 'Are you sure you want to check DUPLICATE BARCODES?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.duplicateExcel', { barcodes: barcodeString });
                },
                onCancel: () => {
                    console.log('Cancel');
                }
            });

        }
        else {
            const notificationWithIcon = (type) => {
                notification[type]({
                    message: 'File Selection Required',
                    description: 'Please choose file first or the selected Textfile has no data found',
                    placement: 'topRight'
                });
            };
            notificationWithIcon('warning');
            return;
        }
    }
}


};
</script>
