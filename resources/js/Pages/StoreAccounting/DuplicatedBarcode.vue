<template>
    <a-tabs>
        <a-tab-pane key="1">
            <template #tab>
                <span style="font-weight: bold;">
                    <TagsOutlined />
                    Duplicated Report (CEBU)
                </span>
            </template>
            <a-card>
                <span :style="{ fontSize: iconSize + 'px' }">
                    <FileSearchOutlined />
                </span>
                <input type="file" ref="fileInput" style="display: none;" @change="handleFileChange" />

                <a-button @click="ChooseFileCebu" style="background-color: #1e90ff; color: white;">
                    Choose File
                </a-button>
                <div style="width: 40%; max-width: 40%;">
                    <span style="color:red;">
                        Selected file:
                    </span>
                    <div style="font-size: small;">{{ cebuFileName ? cebuFileName : "No file selected" }}
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <a-button style="background-color: green; color: white">Check Duplicates</a-button>
                </div>

                <div style="color:red; margin-top: 20px;">
                    <WarningOutlined />
                    Note: Upload Textfile Only!
                </div>

            </a-card>

        </a-tab-pane>
        <a-tab-pane key="2">
            <template #tab>
                <span style="font-weight: bold;">
                    <TagsOutlined />
                    Duplicated Report (ALTA CITTA)
                </span>
            </template>
            <a-card>
                <span :style="{ fontSize: iconSize + 'px' }">
                    <FileSearchOutlined />
                </span>
                <input type="file" ref="fileInput" style="display: none;" @change="handleAltaCittaFileChange"/>

                <a-button @click="chooseFileAltaCitta" style="background-color: #1e90ff; color: white;">
                    Choose File
                </a-button>
                <div>
                    <span style="color:red">
                        Selected file:
                    </span>
                </div>
                <div style="font-size: small;">
                   {{ altaCittaFileName ? altaCittaFileName : "No file selected" }}
                </div>

                <div style="margin-top: 20px;">
                    <a-button style="background-color: green; color: white">Check Duplicates</a-button>
                </div>

                <div style="color:red; margin-top: 20px;">
                    <WarningOutlined />
                    Note: Upload file with CSV extension ONLY.
                </div>

            </a-card>
        </a-tab-pane>
    </a-tabs>
</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            cebuFileName: '',
            altaCittaFileName: '',
            iconSize: 80
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
        }


    }
}
</script>
