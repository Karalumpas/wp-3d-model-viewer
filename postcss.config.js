module.exports = {
  plugins: [
    require('autoprefixer')({
      grid: true,
      flexbox: true,
      supports: true
    }),
    require('postcss-import')({
      path: ['admin/css/src']
    }),
    require('postcss-custom-properties')({
      preserve: false
    }),
    require('postcss-calc')({
      preserve: false
    }),
    require('postcss-nested'),
    ...(process.env.NODE_ENV === 'production' ? [
      require('cssnano')({
        preset: ['default', {
          discardComments: {
            removeAll: true
          },
          normalizeWhitespace: true,
          mergeLonghand: true,
          mergeRules: true,
          minifySelectors: true,
          reduceIdents: false,
          zindex: false
        }]
      })
    ] : [])
  ]
};
