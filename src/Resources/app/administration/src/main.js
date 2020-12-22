import template from './extension/sw-plugin-list/sw-plugin-list.html.twig';

Shopware.Component.override('sw-plugin-list', {
    template,

    inject: ['systemConfigApiService'],

    data() {
        return {
            plugincheckerSettings: {
                'DevertPluginChecker.config.warningText': null,
                'DevertPluginChecker.config.warningActive': null
            }
        };
    },

    created() {
        this.loadPlugincheckerSettings();
    },

    methods: {
        async loadPlugincheckerSettings() {
            this.plugincheckerSettings = await this.systemConfigApiService.getValues('DevertPluginChecker.config')
        },
    },

    computed: {
        plugincheckerWarningText() {
            return this.plugincheckerSettings['DevertPluginChecker.config.warningText'];
        },
        plugincheckerWarningActive() {
            return this.plugincheckerSettings['DevertPluginChecker.config.warningActive'];
        }
    }
});
