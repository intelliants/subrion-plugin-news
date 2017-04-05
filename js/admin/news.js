Ext.onReady(function () {
    var pageUrl = intelli.config.admin_url + '/news/news/';

    if (Ext.get('js-grid-placeholder')) {
        var urlParam = intelli.urlVal('status');

        intelli.news =
            {
                columns: [
                    'selection',
                    {name: 'title', title: _t('title'), width: 1, editor: 'text'},
                    {name: 'date_added', title: _t('date_added'), width: 170, editor: 'date'},
                    {name: 'date_modified', title: _t('date_modified'), width: 170, hidden: true},
                    'status',
                    'update',
                    'delete'
                ],
                storeParams: urlParam ? {status: urlParam} : null,
            };

        intelli.news = new IntelliGrid(intelli.news, false);
        intelli.news.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: intelli.news.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.news);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.news, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        if (urlParam) {
            Ext.getCmp('fltStatus').setValue(urlParam);
        }

        intelli.news.init();
    }
    else {
        $('#input-title, #input-alias').on('blur', function () {
            var alias = $('#input-alias').val();
            var title = alias != '' ? alias : $('#input-title').val();

            if ('' != title) {
                $.get(pageUrl + 'read.json', {get: 'alias', title: title}, function (data) {
                    if ('' != data.url) {
                        $('#title_url').text(data.url);
                        $('#title_box').fadeIn();
                    }
                    else {
                        $('#title_box').hide();
                    }
                });
            }
            else {
                $('#title_box').hide();
            }
        });
    }
});