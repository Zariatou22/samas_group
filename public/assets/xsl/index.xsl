<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="2.0"
	xmlns:html="http://www.w3.org/TR/REC-html40"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Sitemap</title>
				<style type="text/css">
					body {
						font-family: Helvetica, Arial, sans-serif;
						font-size: 13px;
						color: #545353;
					}
					table {
						border: none;
						border-collapse: collapse;
					}
					#sitemap tr:nth-child(odd) td {
						background-color: #eee !important;
					}
					#sitemap tbody tr:hover td {
						background-color: #ccc;
					}
					#sitemap tbody tr:hover td, #sitemap tbody tr:hover td a {
						color: #000;
					}
					#content {
						margin: 0 auto;
						width: 1000px;
					}
					th {
						text-align:left;
						padding-right:30px;
						font-size:11px;
					}
					thead th {
						border-bottom: 1px solid #000;
					}
					td {
						font-size:11px;
					}
					a {
						color: #000;
						text-decoration: none;
					}
					a:visited {
						color: #777;
					}
					a:hover {
						text-decoration: underline;
					}
					.expl {
						margin: 18px 3px;
						line-height: 1.2em;
					}
					.expl a {
						color: #da3114;
						font-weight: 600;
					}
					.expl a:visited {
						color: #da3114;
					}
				</style>
			</head>
			<body>
				<div id="content">
					<p class="expl">
						AKLAB est une entreprise informatique basée au Togo. Elle vous conçoit des sites avec une originalité sans équivoque. Nous n'utilisons pas les outils standards de conception afin de vous offrir un travail de qualité. N'hésitez pas à nous contacter au: (+228) 70 45 56 85 / 99 70 23 39. Visitez notre site web: <a href="https://aklab.tg" target="_blank">https://aklab.tg</a>
					</p>
					<table id="sitemap" width="100%" cellpadding="3">
						<thead>
							<tr>
								<th>URL</th>
								<th>Dernière modification</th>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
								<tr>
									<td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
									<td><xsl:value-of select="sitemap:lastmod"/></td>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>