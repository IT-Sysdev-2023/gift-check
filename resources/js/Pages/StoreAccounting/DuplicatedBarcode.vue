<template>
    <a-card>
        <a-tabs>
            <a-tab-pane key="1">
                <template #tab>
                    <span style="font-weight: bold;">
                        Duplicated Report (CEBU)
                    </span>
                </template>
                <a-card style="width: 72%; margin-left: 28%;">
                    <a-tabs>
                        <a-tab-pane key="1">
                            <template #tab>
                                <span>
                                    Tagbilaran
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tagbilaran
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.tagbilaran.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.tagbilaran" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                        <a-tab-pane key="2">
                            <template #tab>
                                <span>
                                    Talibon
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Talibon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.talibon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.talibon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                        <a-tab-pane key="3">
                            <template #tab>
                                <span>
                                    Tubigon
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tubigon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.tubigon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.tubigon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                    </a-tabs>
                </a-card>
                <a-card style="width: 25%; position: absolute; top:0;">
                    <div style="margin-left: 50px;">
                        <span style="color:green">
                            <TagsOutlined />
                        </span>
                        <span style="font-weight: bold;">
                            Duplicate Barcodes
                        </span>
                        <div style="margin-left: 50px; color:#1e90ff; font-weight: bold;">
                            CEBU
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

                <!-- {{ barcodes}} -->
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span style="font-weight: bold;">
                        Duplicated Report (ALTTA CITTA)
                    </span>
                </template>

                <a-card style="width: 72%; margin-left: 28%;">
                    <a-tabs>
                        <a-tab-pane key="1">
                            <template #tab>
                                <span>
                                    Tagbilaran
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tagbilaran
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.tagbilaran.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.tagbilaran" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                        <a-tab-pane key="2">
                            <template #tab>
                                <span>
                                    Talibon
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Talibon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.talibon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.talibon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                        <a-tab-pane key="3">
                            <template #tab>
                                <span>
                                    Tubigon
                                </span>
                            </template>
                            <a-card>
                                <div style="padding: 10px; background-color: #b0c4de;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tubigon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.tubigon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.tubigon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                    </a-tabs>
                </a-card>

                <a-card style="width:25%; position: absolute; top:0;">
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
                    <!-- {{ data }} -->

                </a-card>

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
import Pagination from '@/Components/Pagination.vue';


export default {
  components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        altta: Object,
        cebu: Object,
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
            altaBarcode: [],
            alttaTable: [
                {
                    title: 'Transno',
                    dataIndex: 'transno'
                },
                {
                    title: 'Name',
                    dataIndex:'name'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Terminal',
                    dataIndex: 'terminal'
                },
                {
                    title: 'Business Unit',
                    dataIndex: 'bu'
                },
                {
                    title: 'Amount',
                    dataIndex: 'amount'
                },
                {
                    title: 'Verdate',
                    dataIndex: 'verdate'
                },
                {
                    title: 'Vertime',
                    dataIndex: 'vertime'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },
                
            ],
            cebuTable: [
                {
                    title: 'Transno',
                    dataIndex: 'transno'
                },
                {
                    title: 'Name',
                    dataIndex: 'name'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Terminal',
                    dataIndex: 'terminal'
                },
                {
                    title: 'Business Unit',
                    dataIndex: 'bu'
                },
                {
                    title: 'Amount',
                    dataIndex: 'amount'
                },
                {
                    title: 'Verdate',
                    dataIndex: 'verdate'
                },
                {
                    title: 'Vertime',
                    dataIndex: 'vertime'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },
            ]
        };
    },
    methods: {
        alttaGenerateExcel() {
            const data = this.altta.alttaTable
            if (this.altta.alttaTable === null) {
                const notificationWarning = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before generating EXCEL',
                        placement: 'topRight'
                    });
                };
                notificationWarning('warning');
                return;
            }
            Modal.confirm({
                title: 'Notification',
                content: 'Are you sure you want generate EXCEL?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.barcodes', { barcodes: data })
                },
                onCancel: () => {
                    console.log('Cancel');
                }

            });
        },
         
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
                        description: 'Please select Textfile type only',
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
                        window.location.href = route('storeaccounting.DuplicatedBarcodes', { AlttaData: altaBarcodeString });
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
        cebuGenerateExcel() {
            if (this.cebu.cebu === null) {
                const notificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before generating EXCEL',
                        placement: 'topRight'
                    });

                };
                notificationWithIcon('warning');
                return;
            }
            const data = this.cebu.cebu
            Modal.confirm({
                title: 'Notification',
                content: 'Are you sure you want to generate Excel?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.barcodes', { barcodes: data });
                },
                onCancel() {
                    console.log('Cancel');
                },

            });
          
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
                    description: 'Please select Textfile type only',
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
                    window.location.href = route('storeaccounting.DuplicatedBarcodes', { barcodes: barcodeString });
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
