package com.trackvia.api.client;        
import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

/**
 * Example
 * 
 * A simple example that will read from the TrackVia api and output
 * a list of apps.
 * 
 * @copyright Copyright (c) 2012, TrackVia Inc.
 */
public class Example {
    
    private static String CLIENT_ID;
    private static String CLIENT_SECRET;
    private static String USERNAME;
    private static String PASSWORD;
    private String accessToken = new String();
    
    public static void main(String[] args) {
        Example e = new Example();
        e.printApps();
        System.out.println("This works");
    }
    
    public Example() {
        if(CLIENT_ID == null || CLIENT_SECRET == null || USERNAME == null || PASSWORD == null){
            String msg = "Unable to create new API connection, please make sure to setup the"+
                    " login credentials in the Example.java before executing";
            throw new RuntimeException(msg);
        }
    }
    
    private void printApps() {
        APIClient client = new APIClient(CLIENT_ID);
		APIStrategy strategy = new LoginStrategy(USERNAME, PASSWORD, CLIENT_SECRET); 
		client.execute( strategy, "get" );
		accessToken = ((LoginStrategy)strategy).getAccessToken();
		strategy = new AppsStrategy(accessToken);
		String responseBody = client.execute(strategy, "get");
		List<Object> list = getResponseAsList( responseBody );
		printListResponse(list);
    }

    
    private void printListResponse(List<Object> list) {

		System.out.println(list.toString());

	}

    private List<Object> getResponseAsList(String responseBody)  {
		List<Object> values = new ArrayList<Object> ();
		ObjectMapper mapper = new ObjectMapper();

		try {
			values = mapper.readValue(responseBody, List.class);
		} catch (JsonParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JsonMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return values;
	}

}
