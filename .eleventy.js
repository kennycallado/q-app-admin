import fs from 'node:fs'
import { $ } from 'zx'

import htmlmin from 'html-minifier'
import prettier from 'prettier'

import pluginWebc from '@11ty/eleventy-plugin-webc'
import pluginBundle from '@11ty/eleventy-plugin-bundle'

export const config = {
  templateFormats: ['html', 'md'],
  htmlTemplateEngine: 'webc',
  dir: {
    input: 'templates',
    output: 'src/App/Views/',
    layouts: '_includes/layouts',
  },
}

/** @param {import("@11ty/eleventy").UserConfig} eleventyConfig */
export default function (eleventyConfig) {
  // change default [permalinks](https://www.11ty.dev/docs/data-eleventy-supplied/#changing-your-projects-default-permalinks)
  eleventyConfig.addGlobalData("permalink", () => {
		return (data) => `${data.page.filePathStem}.${data.page.outputFileExtension}`;
	});

  eleventyConfig.setWatchThrottleWaitTime(500)

  eleventyConfig.addPlugin(pluginWebc, {
    components: 'templates/_includes/components/**/*.webc',
  })

  eleventyConfig.addPlugin(pluginBundle, {
    toFileDirectory: '../../../public/assets/bundle',
  })

  // assets
  eleventyConfig.addPassthroughCopy({
    'templates/_includes/assets': 'assets',
    // 'templates/_includes/assets': '../../../public/assets',
  })

  /** no separation..
  eleventyConfig.addBundle('css', {
    toFileDirectory: '../../../public/assets/bundle/css',
  })

  eleventyConfig.addBundle('js', {
    toFileDirectory: '../../../public/assets/bundle/js',
  })
  */

  // remove previous build
  eleventyConfig.on(
    'eleventy.before',
    async () => $`
# rm -fr ./public/assets/bundle
# rm -rf ./src/App/Views/* ./public/assets/*
`,
  )

  // update assets path
  eleventyConfig.on('eleventy.after', async (ctx) => {
    const pattern = '/../../../public'

    ctx.results.forEach((result) => {
      if (result.content.includes(pattern)) {
        result.content = result.content.replaceAll(pattern, '/public')

        if (ctx.outputMode === 'fs') {
          fs.writeFile(result.outputPath, result.content, (err) => {
            if (err) console.error(err)
            else console.log(`Assets path updated: ${result.outputPath}`)
          })
        }
      }
    })
  })

  // move assets to public
  eleventyConfig.on(
    'eleventy.after',
    async () =>
      $`mv -n ./src/App/Views/assets/* ./public/assets && rm -rf ./src/App/Views/assets`,
  )

  eleventyConfig.addTransform('htmlmin', async function (content, outputPath) {
    if ((outputPath || '').endsWith('.html')) {
      let close = /\s*\{%\s*endif\s*%\}\s*/
      let open = /\s*\{%\s*if\s*%\}\s*/

      return htmlmin.minify(content, {
        customAttrAssign: [open, close],
        collapseWhitespace: false,
        preserveLineBreaks: false,
        removeComments: true,
        minifyCSS: true,
        minifyJS: true,
      })
    }

    return content
  })

  eleventyConfig.addTransform('prettier', async (content, outputPath) => {
    if (outputPath.endsWith('.html')) {
      return prettier.format(content, {
        plugins: [import('prettier-plugin-jinja-template')],
        htmlWhitespaceSensitivity: 'ignore',
        singleAttributePerLine: true,
        parser: 'jinja-template',
        bracketSameLine: true,
        singleQuote: true,
        printWidth: 96,
        semi: false,
      })
    }
    return content
  })
}
