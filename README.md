<h1>GenerateNavigation</h1>

<h2>A Module for WebsiteBaker and Lepton CMS</h2>

<h2>What is it all about?</h2>

<p>The code snippet <code>GenerateNavigation</code> is designed to generate navigations (Also Bootstrap). Create the GenerateNavigation Object (<em>object oriented</em>) where you want the navigation to appear on your frontend. Optional configuration methods allows you to generate the navigation you looking for.</p>

<h2>Download</h2>

<p>You can download an archive of the latest development branch of the <code>GenerateNavigation</code> module using GitHubs <a href="https://github.com/onur-sahin-de/GenerateNavigation/archive/master.zip">ZIP button</a>. The archives of previous module releases can be found in GitHubs <a href="https://github.com/onur-sahin-de/GenerateNavigation/tags">Tags section</a>. The development history is tracked via <a href="https://github.com/onur-sahin-de/GenerateNavigation/commits/master">GitHub</a>.</p>

<p><strong><em>Please note:</em></strong> The archives downloaded from GitHub are not ready for installation in WebsiteBaker, as GitHub includes the archive subfolder. To create a working WebsiteBaker/Lepton installation archive, unpack the downloaded archive on your local computer and zip the contents of the folder <strong><em>GenerateNavigation-master</em></strong> (without the folder itself). Alternatively download an installable archive from the WebsiteBaker <a href="#">module section</a>.</p>

<h2>License</h2>

<p>The <code>GenerateNavigation</code> code snippet is licensed under the <a href="http://www.gnu.org/licenses/gpl-3.0.html">GNU General Public License (GPL) v3.0</a>.</p>

<h2>Requirements</h2>

<p>The minimum requirements to get GenerateNavigation running on your WebsiteBaker installation are as follows:</p>

<ul>
	<li>WebsiteBaker <strong><em>2.8.2</em></strong> or higher</li>
	<li>Lepton CMS <strong><em>1.3.2</em></strong> or higher</li>
</ul>

<h2>Installation</h2>

<ol>
	<li>download archive from <a href="https://github.com/onur-sahin-de/GenerateNavigation/archive/master.zip">GitHub</a></li>
	<li>log into your WebsiteBaker/Lepton backend and go to the <code>Add-ons/Modules</code> section</li>
	<li>install the newly zipped archive via the WebsiteBaker/Lepton installer</li>
</ol>

<h2>Examples</h2>

<h3>Simple Navigation</h3>

<pre>
&nbsp;&nbsp;&nbsp; // Simple Navigation

&nbsp;&nbsp; &nbsp;$simpleNavigation = new GenerateNavigation($wb, $database);

&nbsp;&nbsp; &nbsp;$simpleNavigation-&gt;setCurrentClassName(&quot;active&quot;);

&nbsp;&nbsp; &nbsp;$simpleNavigation-&gt;setMenuID(1);

&nbsp;&nbsp; &nbsp;$simpleNavigation-&gt;printNavigation();</pre>

<p>Simple Navigation Output</p>

<p><strong>Advice:</strong> Screenshot will be uploaded soon</p>

<h3>Standard Bootstrap Navigation</h3>

<pre>
&nbsp;&nbsp;&nbsp; // Bootstrap Navigation

&nbsp;&nbsp; &nbsp;$bootstrapNavigation = new GenerateNavigation($wb, $database);

&nbsp;&nbsp; &nbsp;$bootstrapNavigation-&gt;setCurrentClassName(&quot;active&quot;);

&nbsp;&nbsp; &nbsp;$bootstrapNavigation-&gt;setMenuID(1);

&nbsp;&nbsp; &nbsp;$bootstrapNavigation-&gt;setFurtherNavigationOption(array(

&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;1 =&gt; &#39;&lt;span class=&quot;glyphicon glyphicon-home&quot;&gt;&lt;/span&gt;&#39;,

&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;2 =&gt; &#39;&lt;span class=&quot;glyphicon glyphicon-user&quot;&gt;&lt;/span&gt;&#39;,

&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;3 =&gt; &#39;&lt;span class=&quot;fa fa-shopping-cart&quot;&gt;&lt;/span&gt;&#39;,

&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;4 =&gt; &#39;&lt;span class=&quot;glyphicon glyphicon-envelope&quot;&gt;&lt;/span&gt;&#39;,

&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;5 =&gt; &#39;&lt;span class=&quot;fa fa-file-text&quot;&gt;&lt;/span&gt;&#39;

&nbsp;&nbsp; &nbsp;));

&nbsp;&nbsp; &nbsp;$bootstrapNavigation-&gt;setFormatCode(&quot;[li][a][fno][at][/a]&quot;);

&nbsp;&nbsp; &nbsp;$bootstrapNavigation-&gt;printBootstrapNavigation();
</pre>

<p>Standard Bootstrap Navigation Output</p>

<p><img alt="Bootstrap Navigation Example" src="http://fs2.directupload.net/images/150421/jfbpkfoa.jpg" /></p>
