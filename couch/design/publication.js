{
   "_id": "_design/publication",
   "language": "javascript",
   "views": {
       "year": {
           "map": "function(doc) {  if (doc.year)  {     emit(doc.year, doc._id);  }\n\n\n}"
       }
   }
}