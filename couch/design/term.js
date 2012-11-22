{
   "_id": "_design/term",
   "language": "javascript",
   "views": {
       "tag": {
           "map": "function(doc) {  if (doc.tag)  {    for (var i in doc.tag)     {         emit(doc.tag[i], doc._id);     }  }\n\n\n}"
       },
       "keyword": {
           "map": "function(doc) {  if (doc.keyword)  {    for (var i in doc.keyword)     {         emit(doc.keyword[i], doc._id);     }  }\n\n\n}"
       }
   }
}