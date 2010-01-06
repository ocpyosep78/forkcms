{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td id="treeHolder">
						<div id="treeOptions">
							<div class="buttonHolder">
								<a href="{$var|geturl:"index"}" class="button icon iconBack iconOnly"><span><span><span>{$lblBack|ucfirst}</span></span></span></a>
								<a href="{$var|geturl:"add"}" class="button icon iconAdd"><span><span><span>{$lblAdd}</span></span></span></a>
							</div>
						</div>
						<div id="tree">
							{$tree}
						</div>
					</td>
				</tr>
			</table>

		</td>
		<td id="fullwidthSwitch"><a href="#close">&nbsp;</a></td>
		<td id="contentHolder">
			<div class="inner">
				{form:edit}
					{$txtTitle} {$txtTitleError}
					<div id="pageUrl">
						<div class="oneLiner">
							<p>
								<span><a href="{$SITE_URL}">{$SITE_URL}{$pageUrl}</a></span>
							</p>
						</div>
					</div>
					<div id="tabs" class="tabs">
						<ul>
							<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
							<li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
							<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
							<li><a href="#tabTemplate">{$lblTemplate|ucfirst}</a></li>
							<li><a href="#tabTags">{$lblTags|ucfirst}</a></li>
						</ul>

						<div id="tabContent">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td>
											<div id="editContent">
												{iteration:blocks}
												<div style="display: block;" class="contentA contentBlock" rel="contentA">
													<div class="contentTitle selected hover">
														<table border="0" cellpadding="0" cellspacing="0">
															<tbody><tr>
																<td class="numbering">{$blocks.index}</td>
																<td>
																	<div class="oneLiner">
																		<p><a href="#tabsContent"><span>{$blocks.name}</span></a></p>
																		<p>{$blocks.ddmExtraId}</p>
																	</div>
																</td>
															</tr>
														</tbody></table>
													</div>
													<div class="editContent">
														<fieldset>
															{$blocks.txtHTML}
														</fieldset>
													</div>
												</div>
												{/iteration:blocks}
											</div>
										</td>
										<td id="pagesSide">
											<div id="publishOptions" class="box">
												<div class="heading">
													<h3>Publish</h3>
												</div>
												<div class="options">
													<div class="buttonHolder">
														<a href="{$previewUrl}" class="button icon iconZoom" target="_blank">
															<span><span><span>{$lblPreview|ucfirst}</span></span></span>
														</a>
													</div>
												</div>
												<div class="options">
													<ul class="inputList">
														{iteration:hidden}
														<li>
															{$hidden.rbtHidden}
															<label for="{$hidden.id}">{$hidden.label}</label>
														</li>
														{/iteration:hidden}
													</ul>
												</div>
												<div class="footer">
													<table border="0" cellpadding="0" cellspacing="0">
														<tbody>
															<tr>
																<td><p>Last save: 15:43</p></td>
																<td>
																	<div class="buttonHolderRight">
																		{$btnEdit}
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>

											<div class="box" id="template">
												<div class="heading">
													<h4>Template: About</h4>
													<div class="buttonHolderRight">
														<a href="pages_edit_page.html#tabTemplate" class="button icon iconEdit iconOnly" id="editTemplate"><span><span><span>Edit</span></span></span></a>
													</div>
												</div>
												<div class="options">
													<!-- [A,B],[C,D,0],[E,E,0] -->
													<div class="templateVisual current">
														<table border="0" cellpadding="2" cellspacing="2">
															<tbody>
																<tr>
																	<td class="selected"><a href="#" title="Main content" rel="contentA">A</a></td>
																	<td class="selected"><a href="#" title="Blog" rel="contentB">B</a></td>
																</tr>
															</tbody>
														</table>
														<table border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td><a href="#" title="ZijImage" rel="contentC">C</a></td>
																	<td class=""><a href="#" title="Win win win!" rel="contentD">D</a></td>
																	<td></td>
																</tr>
															</tbody>
														</table>
														<table border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td colspan="2"><a href="#" title="Video" rel="contentE">E</a></td>
																	<td></td>
																</tr>
															</tbody>
														</table>
													</div>

													<table id="templateDetails" class="infoGrid" border="0" cellpadding="0" cellspacing="0">
														<tbody>
														{iteration:blocks}
															<tr>
																<th>{$blocks.index}</th>
																<td>{$blocks.name}</td>
															</tr>
														{/iteration:blocks}
													</tbody>
												</table>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="tabVersions">
						</div>

						<div id="tabSEO">
							<div id="titles" class="box boxLevel2">
								<div class="heading">
									<h3>{$lblTitles|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="meta_pagetitle_overwrite">{$lblPageTitle|ucfirst}</label>
										<span class="helpTxt">{$msgHelpPageTitle}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkPageTitleOverwrite}
											{$txtPageTitle} {$txtPageTitleError}
										</li>
									</ul>
									<p>
										<label for="navigation_title_overwrite">{$lblNavigationTitle|ucfirst}</label>
										<span class="helpTxt">{$msgHelpNavigationTitle}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkNavigationTitleOverwrite}
											{$txtNavigationTitle} {$txtNavigationTitleError}
										</li>
									</ul>
								</div>
							</div>

							<!--
								@todo @tijs
							<div id="seoNofollow" class="box boxLevel2">
								<div class="heading">
									<h3>Nofollow</h3>
								</div>
								<div class="options">
									<fieldset>
										<p class="helpTxt">{$msgHelpNoFollow}</p>
										<ul class="inputList">
											<li>
												{$chkNoFollow}
												<label for="noFollow">{$msgActivateNoFollow|ucfirst}</label>
											</li>
										</ul>
									</fieldset>
								</div>
							</div>
							 -->

							<div id="seoMeta" class="box boxLevel2">
								<div class="heading">
									<h3>{$lblMetaInformation|ucfirst}</h3>
								</div>
								<div class="options">
									<p>
										<label for="meta_description_overwrite">{$lblMetaDescription|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaDescription}</span>
									</p>
									<ul class="inputList">
										<li>
											{$chkMetaDescriptionOverwrite}
											{$txtMetaDescription} {$txtMetaDescriptionError}
										</li>
									</ul>

									<p>
										<label for="meta_keywords_overwrite">{$lblMetaKeywords|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaKeywords}</span>
									</p>

									<ul class="inputList">
										<li>
											{$chkMetaKeywordsOverwrite}
											{$txtMetaKeywords} {$txtMetaKeywordsError}
										</li>
									</ul>

									<p>
										<label for="meta_custom">{$lblMetaCustom|ucfirst}</label>
										<span class="helpTxt">{$msgHelpMetaCustom}</span>
										{$txtMetaCustom} {$txtMetaCustomError}
									</p>
								</div>
							</div>

							<div id="seoUrl" class="box boxLevel2">
								<div class="heading">
									<h3>{$lblURL}</h3>
								</div>
								<div class="options">

									<label for="url_overwrite">{$lblCustomURL|ucfirst}</label>
									<span class="helpTxt">{$msgHelpMetaURL}</span>

									<ul class="inputList">
										<li>
											{$chkUrlOverwrite}
											<span id="urlFirstPart">{$SITE_URL}{$pageUrl}</span>{$txtUrl} {$txtUrlError}
										</li>
									</ul>

								</div>
							</div>
						</div>
						<div id="tabTemplate">
							{$ddmTemplateId} {$ddmTemplateIdError}
						</div>
						<div id="tabTags">
							<div id="tags" class="box boxLevel2">
								<div class="heading">
									<h3>Tags</h3>
								</div>
								<div class="options">
									<!-- <label for="addTag">Add tags:</label> -->
									<div class="oneLiner">
										<p><input class="inputText" id="addTag" type="text"></p>
										<div class="buttonHolder">
											<a href="#" class="button icon iconAdd"><span><span><span>Add</span></span></span></a>
										</div>
									</div>
									<!-- <label>Current tags:</label> -->
									<ul id="tagsList">
										<li><span><strong>Music</strong> <a href="#" title="Delete tag">X</a></span></li>
										<li><span><strong>Concerts</strong> <a href="#" title="Delete tag">X</a></span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="fullwidthOptions">
						<a href="#" class="button linkButton icon iconDelete"><span><span><span>Delete page</span></span></span></a>
						<div class="buttonHolderRight">
							{$btnEdit}
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}