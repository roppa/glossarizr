<!DOCTYPE html>

<head>

       <meta charset="UTF-8">

       <title>Glosserizr</title>

		<script src="/assets/js/jquery.js"></script>
		<!-- <script src="/assets/js/bootstrap-transition.js"></script>
		<script src="/assets/js/bootstrap-alert.js"></script>
		<script src="/assets/js/bootstrap-modal.js"></script>
		<script src="/assets/js/bootstrap-dropdown.js"></script>
		<script src="/assets/js/bootstrap-scrollspy.js"></script>
		<script src="/assets/js/bootstrap-tab.js"></script>
		<script src="/assets/js/bootstrap-tooltip.js"></script>
		<script src="/assets/js/bootstrap-popover.js"></script>
		<script src="/assets/js/bootstrap-button.js"></script>
		<script src="/assets/js/bootstrap-collapse.js"></script>
		<script src="/assets/js/bootstrap-carousel.js"></script>
		<script src="/assets/js/bootstrap-typeahead.js"></script>-->

		<script>
			var apiKey = '1766cba83f05e0a627fbe111ab8ae039';
			var baseUrl = 'https://api.pearson.com/longman/dictionary/0.1';
			var dataFmt = '.json';
			var searchUrl = baseUrl + '/entry' + dataFmt;
			var debugLog;

			function doSearch(){
				debugLog.append('Looking up ' + searchFor + ' using  ' + searchUrl + '<br/>');
			}
						
			function hasAttr (attr) {
				if (typeof attr !== 'undefined' && attr !== false) {
					return true
				} else {
					return false;
				}
			}
			
			function doSearch(searchFor){
			
			       debugLog.append('Looking up ' + searchFor + ' using  ' + searchUrl + '<br/>');
			
			       var data = 'apikey=' + apiKey + '&q=' + searchFor
			       
			       $.ajax({
			
				       type: 'GET',
				       url: searchUrl,
				       data: data,
				       dataType: 'jsonp',
				       jsonp: 'jsonp',
				       success: function(data){
				               handleResponse(data);
				       },
				       error: function(req, err, text ) {
				       		debugLog.append('Error: ' + status + '(' + text + ')<br/>');
				       }
			       }); 
			}
			
			function handleResponse(data){
				debugLog.append('Response received <br/>');
				var results = data.Entries.Entry;
				var html = entry(results);
				$('#resultList').html(html);
				$('li>a').click(function(){                             
					$(this).parent().find("div").toggle(); 
				});
				$('#resultList > li > div').hide();
				$('#resultList > li:first > div').show();
				debugLog.append('Response processed <br/>');
			}
			
			function entry(from){
				var html = '';				
				if ($.isArray(from)){
					for (var idx in from){
						html += entry(from[idx]);
				    }
				} else {
					//debugLog.append("Processing Entry: " + from.Head.HWD['#text'] + '<br/>');
					html += '<li><a>';		
					html += from.Head.HWD['#text'];
					html += '</a><div>';
					var attr = $(from).attr('multimedia');
					if (attr) {
						html += multimedia(from.multimedia);
					}
					html += '<ol id="sense">';					
					html += sense(from.Sense);
					html += '</ol>';
					html += '</div></li>\n';
					//debugLog.append("<br/>Processed Entry: " + from.Head.HWD['#text'] + '<br/>');
				}
				return html;
			}
			
			function multimedia(from) {

				debugLog.append('multimedia ');
				var html='';
				
				if ($.isArray(from)) {
					for (var idx in from) {
						html += multimedia(from[idx]);
					}
				} else {
					var mm_href = from['@href'];
					var mm_type = from['@type'];
					if (mm_type =='EXA_PRON') {
						mm_type ='';
					}

					switch (mm_type){
					
					      case 'EX_PRON':
					             mm_type = '';
					             break;
					      case 'US_PRON':
					             mm_type = 'American pronunciation';
					             break;
					      case 'GB_PRON':
					             mm_type = 'British pronunciation';
					             break;
					      case 'SOUND_EFFECTS':
					             mm_type = 'Sound effect';
					             break;
					}
					
					if (mm_href.match(/\.mp3$/)) {
						html = mm_type + ' <audio controls="controls">' + '<source src="' + baseUrl + mm_href + '?apikey=' + apiKey + '" type="audio/mpeg"/> </audio>';
              		} else if (mm_type == 'DVD_PICTURES') {
                    	html = '<img src="' + baseUrl + mm_href + '?apikey=' + apiKey + '"> </img>';
                    }
                    html += '<br/>';
       			}
       			return html;
       		}
       		
			function sense(from){
			
				debugLog.append('sense ');
				var html='';
				var attr = $(from).attr('Subsense');
				
				if ($.isArray(from)) {
					for (var idx in from){
						html += sense(from[idx]);
					}
				} else if (hasAttr(attr)) {
					html += sense(from.Subsense);
				} else {
				      html += '<li>' + text(from.DEF) + '<br/>';
				      var att_example = $(from).attr('EXAMPLE');
				      var att_lexunit = $(from).attr('LEXUNIT');
				      if (hasAttr(att_example)){
				      	html += example(from.EXAMPLE);
				      } else if (hasAttr(att_lexunit)){
				      	html += example(from.LEXUNIT);
				      }
				      html += '</li>';
				}
				
				return html;

			}

			function example(from){
				debugLog.append('example ');
				var html ='';
	
				if ($.isArray(from)){			
					for (var idx in from){
						html+= example(from[idx]);
					}
				} else {				
					html += '<q>' + text(from) + '</q>';
					var att_multimedia = $(from).attr('multimedia');
					if (hasAttr(att_multimedia)){
						html += multimedia(from.multimedia);
					}
				}
			
				return html;
			}
			
			function text(from){
				
				debugLog.append('text ');
				var result = '';
				var text = from['#text'];
				var nonDv;
				var hasNonDv = $(from).attr('NonDV');
				
				if ($.isArray(text)){
				      for (var idx in text) {
				      	result += text[idx];
				
				              if (hasNonDv){
				
				                     nonDv = from.NonDV[idx];
				
				                     if (nonDv != undefined){
				                            result += nonDv.REFHWD['#text'];
				                     }
				              }
				      }
				
				} else {
				
				      result += text;
				      var att_collioinexa = $(from).attr('COLLOINEXA');
				
				      if (hasNonDv){
				      	result += from.NonDV.REFHWD['#text'];
				      } else if (hasAttr(att_collioinexa)){
				      	result += from.COLLOINEXA['#text'];
				      }
				
				}
				
				return result;
			
			}
			
			$(function() {

				console.log('loaded');	
				$('#list_div').hide();
				$('#entry_div').hide();
				$('#search').click(function(event){
						  console.log('search clicked');
				      event.preventDefault();
				      doSearch($('#searchText').val());
				});
				debugLog = $('#log');
		
			});

		</script>

 </head>

 <body>

 <article>

       <h1>Longman Dictionary API Example</h1>

       <section id="input">

              <h2>Search</h2>

              <form action="#">

                      <p>Enter a word of phrase to search for:

              <input id="searchText" type=text/>

              <button id="search">Search</button></p>

              </form>

       </section>

       <section id="results">

              <h2>Results</h2>

              <ul id="resultList"></ul>

       </section>

       <section id="debug">

              <h2>Debug Log</h2>

              <p id="log"></p>

       </section>

 </article>

 </body>