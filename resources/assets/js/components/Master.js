// Master.js
import React, {Component} from 'react';
import axios from 'axios';
import ReactDOM from 'react-dom';
import Pagination from './pagination';
import { Router, Route, Link } from 'react-router';
var Loader = require('react-loader');

var pages = [];
var lat = 0;
var lng = 0;
var url ='';
var Records = [];
var msg ="";
var err = "";
class Master extends Component {
  constructor(props) {
      super(props);  
       
      this.state = {loaded: false,items: '',pageOfItems: []};
      this.onChangePage = this.onChangePage.bind(this);
      this.onClick = this.onClick.bind(this);

       /*===========================geolocalisation===========================*/
       axios.post('https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyD-zI_JnRhoUUIPZSrK1xQSSdcF7eDb9kI')
       .then(response => {
        lat = response.data.location.lat;
        lng = response.data.location.lng;   
       })
       .catch(function (error) {
         console.log(error);
       })

       /*==========================fin geolocalisation ===========================*/

     }

    
    componentDidUpdate(nextProps) {

		if (this.props.params.type !== nextProps.params.type ) {
		  this.setState({loaded: false})
		  this.componentDidMount();
		}

 	}

     componentDidMount(){
     	msg='';
     	err=''; 
       url = 'http://test.localhost/list?type='+this.props.params.type+'&latitude='+lat+'&longitude='+lng;
       axios.get(url)
       .then(response => {     
      // 	alert(JSON.stringify(response.data.data))
         this.setState({loaded: true, items:response.data.data});  
       })
       .catch(function (error) {
         console.log(error);
       })
     }
    
     
     onChangePage(pageOfItems) {
        this.setState({ pageOfItems: pageOfItems });
    } 

    /***************************like & dislike actions********************************/
    onClick(id,like){
	    var uri = 'http://test.localhost/like?id='+id+'&like='+like;
	    axios.get(uri).then((response) => {
	      if(response.data.msg)
	      		msg = response.data.msg;
	      if(response.data.err)
	       	    err = response.data.err;
	      this.setState({loaded: false})
	      this.componentDidMount();
	    }) .catch(function (error) {
	         alert(error);
	    });
     } 


      /*************************** remove a shop from my preferred shops list********************************/
    onDelete(id){
	    var uri = 'http://test.localhost/delete?id='+id;
        axios.get(uri).then((response) => {
            if(response.data.msg)
	      		msg = response.data.msg;
	      if(response.data.err)
	       	    err = response.data.err;
	      this.setState({loaded: false})
	      this.componentDidMount();
          }) .catch(function (error) {
               alert(error);
        });
     }     

     ListRow(){ 
    
         return this.state.pageOfItems.map(function(object, i){
         	var id = object.record.id
           return(

             	<div className="col-md-2 margin" id="{object.record.id}">
                            <div className="panel panel-primary text-center">
                                <h4 className="text-center">
                                          {object.record.name}
                                    </h4>
                           <img className="margin" src={object.record.picture}/>
                           <div>
                           {object.type == "prefered" ? 
                           <a className="btn btn-success" onClick={this.onDelete.bind(this,object.record.shop_id)} >Remove</a>:''}
                           
                           {object.type != "prefered" && object.type !='nearby' ? 
                           <a className="btn btn-success" onClick={this.onClick.bind(this,id,'like')} >LIKE</a>:''}
                           {object.type != "prefered" && object.type !='nearby' ? 
                           <a className="btn btn-danger" onClick={this.onClick.bind(this,id,'dislike')}>DISLIKE</a>:''}
                         

                           </div>
                            </div> 
                        </div>

             )
         }.bind(this))
     }


  render(){
    return (
      <div className="container">
    <div className="row">
        <nav className="navbar navbar-right">     
            <ul className="nav navbar-nav">
             <li><Link to="/" >Home</Link></li>
              <li><Link to="/nearby" >Nearby shop</Link></li>
              <li><Link to="/prefered">My prefered shops</Link></li>
            </ul>      
      </nav>
       </div>
          <div className="row">

          <h1>Test mariama zarkan</h1>
          	{msg !="" ? 
          		<div class="text-success">
				  <strong>Success!</strong> {msg} 
				</div>
				:''
          	}


          	{err !="" ? 
          		<div class="alert text-warning">
				  <strong>Warning!</strong> {err} 
				</div>
				:''
          	}

  
              <div class="panel-body"> 
              	<Loader loaded={this.state.loaded}>
                  {this.ListRow()}
                  <Pagination items={this.state.items} onChangePage={this.onChangePage} />
                </Loader>
              </div>         
      </div>
  </div>
    )
  }
    
}
export default Master;